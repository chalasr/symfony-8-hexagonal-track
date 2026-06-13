# symfony-8-hexagonal-track

Companion repository for **Symfony 8: The Hexagonal Track** —
SymfonyLive Montreal 2026 ([deck](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track),
[outline](../outline.md), [spec](../demo-repo-spec.md)).

Two bounded contexts in one app:

- `src/BookStore/` — full hexagonal (Domain / Application / Infrastructure).
- `src/Subscription/` — RAD (single entity, single controller).

## Quickstart

**Docker + FrankenPHP** (zero PHP/Composer install needed):

```bash
docker compose up --build      # builds the image, runs schema:create on first boot
# app is now on http://localhost:8000
```

**Bare metal** (needs PHP 8.4+ and Composer):

```bash
composer install
bin/console doctrine:schema:create
symfony serve                  # or: php -S 127.0.0.1:8000 -t public
```

Then:

```bash
# Discount a book ([slide 20](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=20))
curl -X POST http://localhost:8000/books/<uuid>/discount \
     -H 'Content-Type: application/json' \
     -d '{"percentage": 25}'                                              # → 204

# Cheapest books ([slide 23](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=23))
curl http://localhost:8000/books/cheapest?size=5                          # → 200 JSON

# Same use case from the CLI ([slide 20bis](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=21))
bin/console books:discount <uuid> 25

# Create a subscription (RAD path, [slide 28](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=28))
curl -X POST http://localhost:8000/subscriptions \
     -H 'Content-Type: application/json' \
     -d '{"email": "you@example.com"}'                                    # → 201
```

## Slide ↔ file map

| Slide | File |
|---|---|
| [11 — pristine `Book`](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=11) | `src/BookStore/Domain/Model/Book.php` |
| [12 — VO at language level](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=12) | `src/BookStore/Domain/ValueObject/Price.php` + `Domain/Exception/InvalidPriceException.php` |
| [13 — asymmetric visibility](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=13) | `src/BookStore/Domain/Model/Book.php` |
| [14 — property hooks](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=14) | `src/BookStore/Domain/Model/Book.php` (`$displayName`) |
| [16 — ports stay](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=16) | `src/Shared/Application/{Command,Query}/*` |
| [18 — `DiscountBookCommand`](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=18) | `src/BookStore/Application/Command/DiscountBookCommand.php` |
| [19 — `#[AsMessageHandler]`](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=19) | `src/BookStore/Application/Command/DiscountBookHandler.php` |
| [20 — POST adapter](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=20) | `src/BookStore/Infrastructure/Http/DiscountBookController.php` + `Application/Dto/DiscountBookPayload.php` |
| [20bis — same use case, any context](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=21) | HTTP: `…/Http/DiscountBookController.php` · **CLI:** `src/BookStore/Infrastructure/Cli/DiscountBookCliCommand.php` (`bin/console books:discount <id> <pct>`) |
| [22 — Query + handler](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=22) | `src/BookStore/Application/Query/FindCheapestBooks{Query,Handler}.php` |
| [23 — GET adapter](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=23) | `src/BookStore/Infrastructure/Http/CheapestBooksController.php` + `Application/Dto/CheapestBooksFilter.php` |
| [24 — `#[ExtendsValidationFor]`](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=24) | `src/BookStore/Infrastructure/Validation/BookValidation.php` |
| [25 — ObjectMapper](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=25) | `src/BookStore/Application/Dto/BookResource.php` |
| [28 — RAD coexistence](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=28) | `src/Subscription/Entity/Subscription.php` (`#[ORM\*]` + `#[Assert\*]`, no Route) + `src/Subscription/Controller/SubscriptionController.php` (`#[MapRequestPayload] Subscription` direct, no input DTO) |
| [29 — object-pure violation](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=29) | `src/BookStore/Domain/Model/Book.php` (`#[ORM\Entity]` + `#[ORM\Embedded]`) |
| [31 — deptrac config](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=31) | `deptrac_hexa.yaml` |
| [32 — two-tier tests](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=32) | `tests/BookStore/` |

## Domain invariants — two-level guard

Every value object enforces its invariants at construct time and throws a
dedicated `InvariantViolationException` subclass:

| VO | Refuses | Exception |
|---|---|---|
| `Price` | negative amount | `InvalidPriceException` |
| `BookName` | blank / whitespace-only / `mb_strlen > 255` | `InvalidBookNameException` |
| `Discount` | `< 1` or `> 100` (no-op discount rejected) | `InvalidDiscountException` |
| `AggregateRootId` / `BookId` | non-UUID string | `InvalidIdentifierException` |

A matching first-level guard sits on every adapter — Validator or Routing
catches bad input and returns 422/404 before the domain ever runs:

| Adapter guard | File | Mirrors |
|---|---|---|
| `Range(min: 1, max: 100)` | `DiscountBookPayload::percentage` | `InvalidDiscountException` |
| `Requirement::UUID` on `{id}` | `DiscountBookController` route | `InvalidIdentifierException` |
| `NotBlank` + `Length(1, 255)` | `BookValidation::$name` (via `#[ExtendsValidationFor]`) | `InvalidBookNameException` |
| `PositiveOrZero` | `BookValidation::$price` (via `#[ExtendsValidationFor]`) | `InvalidPriceException` |

The VO is the last-resort backstop. The domain refuses invalid states by
construction.

## Tests — two-tier rule ([slide 32](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=32))

One port (`BookRepositoryInterface`), two adapters:

- `DoctrineBookRepository` runs in **one** test
  (`tests/BookStore/Infrastructure/Doctrine/DoctrineBookRepositoryTest.php`) —
  the SQL/ORM contract, against real SQLite.
- `InMemoryBookRepository` runs in **every other test** — same port, no DB,
  milliseconds per test.

`config/services_test.yaml` aliases the port to in-memory for the whole test
env; the Doctrine test rebinds it for itself via
`$container->set(BookRepositoryInterface::class, $doctrineImpl)`. Doubles
aren't mocks — they're full alternative implementations of the same port.

```bash
bin/phpunit                                  # 37 tests, all green
bin/phpunit tests/BookStore/Application      # handler tests only — < 100ms total
bin/phpunit tests/BookStore/Domain           # VO invariant tests
```

## Architecture enforcement — deptrac

```bash
vendor/bin/deptrac analyse --config-file=deptrac_hexa.yaml
```

The `Attributes` lane encodes Noback's "object-pure" rule
([slide 31](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=31)):
attribute classes (`ORM\Mapping`, `Validator\Attribute|Constraints`,
`Serializer\Attribute`, `ObjectMapper\Attribute`, `Messenger\Attribute`,
`HttpKernel\Attribute`, `Routing\Attribute`) are metadata, not IO, so they cross
into Domain/Application by design. The leak is intentional, bounded, machine-checked.

## Stack

- **PHP 8.4+** — load-bearing: asymmetric visibility ([slide 13](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=13)), property hooks ([slide 14](https://speakerdeck.com/chalasr/symfony-8-the-hexagonal-track?slide=14))
- **Symfony 8.1** (released 2026-05-29) — `#[Serialize]`, HTTP-less kernel, `#[AsTagDecorator]`, variadic `#[MapRequestPayload]`
- **Doctrine ORM 3** / **DBAL 4** / **DoctrineBundle 3** — SQLite for zero-setup demo
- **FrankenPHP** (`dunglas/frankenphp:php8.4`) as the container runtime — classic mode, no worker, no special bootstrap
- **Deptrac 4.6** for architecture rules — full PHP 8.4 parser support
- **PHPUnit 11** with `bin/phpunit` shim

## Going further — API Platform × DDD

For a full hypermedia RESTful API on top of the same hexagonal substrate, see
[**mtarld/apipddd**](https://github.com/mtarld/apipddd) — the companion repo
for the previous talk Robin Chalas and Mathias Arlaud gave on the topic
([YouTube](https://www.youtube.com/watch?v=SSQal3Msi9g)). API Platform is the
superior choice for full-featured hypermedia REST: content negotiation, JSON-LD
/ Hydra / HAL, OpenAPI, filters, pagination, validation — out of the box.
