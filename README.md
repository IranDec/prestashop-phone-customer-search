# PrestaShop Phone Customer Search

A small back-office module by **Mohammad Babaei** that adds customer lookup by phone number to PrestaShop's manual order creation page.

- Author: Mohammad Babaei
- Website: [adschi.com](https://adschi.com)
- Module name: `phonecustomersearch`
- Current version: 1.1.0
- License: MIT

## What it does

The module adds a phone search field to the administrator's **Orders → New order** page. It queries customer address phone/mobile fields, returns matching customer records, and lets the employee choose a customer for the order. The lookup is performed on demand; the initial customer list/loading behavior of other modules is not changed.

Search supports Persian and Arabic numerals as well as Latin digits. Requests are debounced, previous in-flight requests are cancelled, and the interface displays a loading state while waiting for results. The module waits for a minimum of seven digits before searching to reduce unnecessary database queries.

## Requirements and compatibility

- PrestaShop 1.7.8 or newer, including PrestaShop 8 and 9 according to the module's declared compatibility range.
- PHP version supported by the installed PrestaShop release.
- The module uses the PrestaShop module and AdminController APIs; behavior may vary with customized back-office themes or order-creation overrides.

The module was developed against an existing PrestaShop installation. The full compatibility range has not been independently verified against every PrestaShop/PHP combination. Test on a staging installation before production use.

## Installation

1. Download the repository archive from GitHub and extract it.
2. In PrestaShop's **Modules → Module Manager**, upload a ZIP containing the `phonecustomersearch/` directory, or copy that directory to `modules/phonecustomersearch/` on the server.
3. Install **Phone customer search** from Module Manager.
4. Open **Orders → New order** and search using at least seven digits of a customer's phone number.

To create an upload ZIP from the repository root:

```sh
zip -r phonecustomersearch.zip phonecustomersearch
```

## Upgrade

Upload/replace the `phonecustomersearch/` module directory with the new version. The included upgrade script is used by PrestaShop's module upgrade mechanism. Keep a database and files backup before production upgrades.

## Privacy and security

- The module searches customer contact details already stored by the shop and displays matching results only to authenticated back-office users with access to manual order creation.
- It does not send customer information to an external service.
- Do not expose the generated Admin controller URL or back-office credentials publicly.

## Troubleshooting

- Confirm the module is installed and that you are on the manual order creation page.
- Enter at least seven digits; Persian, Arabic, and Latin numerals are accepted.
- Check browser developer tools and the PrestaShop/PHP logs for Admin controller errors.
- Customized themes or overrides can alter the page markup and may require compatibility adjustments.

## Development

The module is plain PHP, JavaScript, and CSS. Keep changes scoped to the manual order page and avoid logging or committing customer data, credentials, session tokens, or production configuration.

## License

MIT. See [LICENSE](LICENSE).
