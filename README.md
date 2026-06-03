# symfony-8-hexagonal-track

Companion repository for **Symfony 8: The Hexagonal Track** — SymfonyLive Montreal 2026
([outline](../outline.md), [spec](../demo-repo-spec.md)).

Two bounded contexts in one app:

- `src/BookStore/` — full hexagonal (Domain / Application / Infrastructure).
- `src/Subscription/` — RAD (single entity, single controller).

## Quickstart

```bash
composer install
bin/console doctrine:schema:create
symfony serve            # or: php -S 0.0.0.0:8000 -t public
```

Then:

```bash
# Discount a book (slide 20)
curl -X POST http://localhost:8000/books/<uuid>/discount \
     -H 'Content-Type: application/json' \
     -d '{"percentage": 25}'

# Cheapest books (slide 23)
curl http://localhost:8000/books/cheapest?size=5

# Create a subscription (RAD path, slide 28)
curl -X POST http://localhost:8000/subscriptions \
     -H 'Content-Type: application/json' \
     -d '{"email": "you@example.com"}'
```

## Slide ↔ file map

| Slide | File |
|---|---|
| 11 — pristine `Book` | `src/BookStore/Domain/Model/Book.php` |
| 12 — VO at language level | `src/BookStore/Domain/ValueObject/Price.php` |
| 13 — asymmetric visibility | `src/BookStore/Domain/Model/Book.php` |
| 14 — property hooks | `src/BookStore/Domain/Model/Book.php` (`$displayName`) |
| 16 — ports stay | `src/Shared/Application/{Command,Query}/*` |
| 18 — `DiscountBookCommand` | `src/BookStore/Application/Command/DiscountBookCommand.php` |
| 19 — `#[AsMessageHandler]` | `src/BookStore/Application/Command/DiscountBookHandler.php` |
| 20 — POST adapter | `src/BookStore/Infrastructure/Http/DiscountBookController.php` + `Application/Dto/DiscountBookPayload.php` |
| 20bis — Same use case, any context | HTTP: `…/Http/DiscountBookController.php` · **CLI:** `src/BookStore/Infrastructure/Cli/DiscountBookCliCommand.php` (`bin/console books:discount <id> <pct>`) |
| 22 — Query + handler | `src/BookStore/Application/Query/FindCheapestBooks{Query,Handler}.php` |
| 23 — GET adapter | `src/BookStore/Infrastructure/Http/CheapestBooksController.php` + `Application/Dto/CheapestBooksFilter.php` |
| 24 — `#[ExtendsValidationFor]` | `src/BookStore/Infrastructure/Validation/BookValidation.php` |
| 25 — ObjectMapper | `src/BookStore/Application/Dto/BookResource.php` |
| 28 — RAD coexistence | `src/Subscription/Entity/Subscription.php` + `src/Subscription/Controller/SubscriptionController.php` |
| 29 — object-pure violation | `src/BookStore/Domain/Model/Book.php` (`#[ORM\Entity]` + `#[ORM\Embedded]`) |
| 31 — deptrac config | `deptrac_hexa.yaml` |
| 32 — two-tier tests | `tests/BookStore/` |

## Tests — two-tier rule (slide 32)

One repository port — `App\BookStore\Domain\Repository\BookRepositoryInterface` — two adapters:

- `DoctrineBookRepository` runs in **one** integration test
  (`tests/BookStore/Infrastructure/Doctrine/DoctrineBookRepositoryTest.php`).
  That's where the SQL/ORM contract is verified — against real SQLite.
- `InMemoryBookRepository` runs in **every other test** — handler tests, controller tests, end-to-end.
  Same port contract, no database, milliseconds per test.

Wiring lives in `config/services_test.yaml`: it aliases the port to the in-memory adapter for the whole
test env. The Doctrine integration test overrides the alias **for itself only** via
`self::getContainer()->set(BookRepositoryInterface::class, $doctrineImpl)`.

This isolates "what the handler does" from "what the database does" — two questions, two test tiers,
two adapters. Doubles aren't mocks; they're full alternative implementations of the same port.

```bash
bin/phpunit                                  # all tests
bin/phpunit tests/BookStore/Application      # handler tests only — should be < 100ms total
```

## Architecture enforcement — deptrac

```bash
vendor/bin/deptrac analyse --config-file=deptrac_hexa.yaml
```

The `Attributes` lane is the deptrac-encoded version of Matthias Noback's "object-pure" rule
(slide 31 speaker note): attribute classes are metadata, not IO, so they're allowed into
Domain/Application even though they live under `Symfony\\` and `Doctrine\\` namespaces.
The leak is intentional, bounded, and machine-checked.

## Stack

- PHP `>= 8.4` (asymmetric visibility + property hooks are load-bearing for slides 13–14)
- Symfony `^8.1`
- Doctrine ORM `^3` / DBAL `^4` / **DoctrineBundle `^3`** on SQLite for zero-setup demo
- Deptrac for architecture rules
- PHPUnit 11

## Known caveats (as of 2026-05-31)

- **Deptrac can't parse PHP 8.4 `private(set)` yet.** `qossmic/deptrac` 2.0.4 ships with an older
  `nikic/php-parser` that errors on the asymmetric-visibility keyword (see `src/BookStore/Domain/Model/Book.php`).
  The `deptrac_hexa.yaml` config is still a faithful artefact of the architecture rules
  (and is what slide 31 shows); it just doesn't run green today. On stage, mention it: *"so new
  even deptrac's parser hasn't caught up — the rule holds in your CI as soon as it does."*
- **Composer constraints differ from `demo-repo-spec.md`:** `doctrine/doctrine-bundle` is `^3` (not `^2` — 2.x can't ship Sf 8.x console),
  and the deptrac package is `qossmic/deptrac` (not the abandoned `qossmic/deptrac-shim`).
- **Verified, not run.** Per the speaker's call (slides over runnable demo), this repo is showcase code:
  `composer install` succeeds, `php -l` is clean over every source file, `composer dump-autoload`
  loads 5003 classes, and all four `[VERIFY]` namespaces (`Serialize`, `ExtendsValidationFor`,
  `ExtendsSerializationFor`, `Map`) exist at the documented vendor paths. Live `bin/phpunit`,
  Doctrine schema creation, `symfony serve`, and the controller WebTestCase round-trips were
  *not* run end-to-end against this scaffold.
