
## Tech Stack
- Laravel 11 with Jetstream and Inertia Vue 3 starter kit
- Jetstream disabled features
  - API support
  - Account deletion
  - Email verification
  - Registration
  - Two-factor Authentication
- Coding style
  - [Pint](https://laravel.com/docs/11.x/pint) for php & Laravel.  
  - [Es-lint](https://eslint.org/docs/latest/use/getting-started#quick-start) for Javascript and Vue.
 - Commands 
 ```shell
    # run es lint
    npm run eslint
  
    # run pint
    ./vendor/bin/pint
  
    # run tests
    php artisan test
  ```

## UI blocks
- `AppLayout`
  - `Container`
    - Headings: `PageTitle` `PageBackButton`
