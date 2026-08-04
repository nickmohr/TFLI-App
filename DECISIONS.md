# Decisions

This project was coded in a similar style and architecture to how I would normally build an application, to give you an idea of how I work. Controllers, Models, and Views are all contained within the `app` folder. I also prefer to use a Service layer as a bridge between controllers and models. This allows models to focus primarily on handling data for a single object without including peripheral business logic, although some overlap between services and models can occur.

All of my projects include PHPStan and PHP CS Fixer, so Composer was used to install and manage these dependencies. Using Composer also allowed me to take advantage of PSR-4 autoloading without having to implement a custom autoloader.

The `public` folder contains a single `index.php` file, which acts as the application's entry point. `.htaccess` will point all other routes to it when running on Apache.

A custom router was written which, in hindsight, was probably overkill for a project of this size. It receives incoming requests and dispatches them to the appropriate controller method via `app/app.php`. From there, controllers interact with models and services to handle URL validation, filtering, processing, and retrieval.

A simple View service was created to render templates for output. Templates remain as plain PHP files for simplicity and to avoid introducing an additional templating dependency.

On the client side, pages are styled using basic Tailwind CSS v4 classes. Client-side validation is implemented with vanilla JavaScript and takes advantage of Tailwind's `:valid` and `:invalid` state styling and HTML5 `checkValidity` functions.

# Security
Every effort was made to make sure all input was validated and filtered appropiately. Database calls used PDO and are parametised. All variables on templates are escaped.

# Compromises

- As this project was never intended for production use, limited effort was spent on hardening security features such as Content Security Policy (CSP), secure cookies, and session authentication. While a CSP is present, stricter policies and features such as insecure request upgrades have not been implemented.
- No database migration system was included. Instead, the application's single table is created (if necessary) at the beginning of each request. This trade-off simplifies setup and testing although is obviously a bad idea in a production environment.
- Limited effort was spent on handling edge cases in the router and production-level error scenarios.
- Tailwind was included from the CDN, ideally it'd be loaded locally.

# With Extra Time

- PHPUnit tests are the obvious missing feature in this project.
- Harden CSP and cookie settings.
- Load tailwind locally using Vite.
- Add rate limiting.
- Create a proper installation and setup script.
- Improve routing and add protection against additional edge cases.
- Enhance the overall styling and user experience.

