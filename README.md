# ProConvey [![Actions](https://github.com/coreblueltd/preconvey/actions/workflows/tests.yml/badge.svg?branch=develop)](https://github.com/coreblueltd/preconvey/actions/workflows/tests.yml) [![Actions](https://github.com/coreblueltd/preconvey/actions/workflows/staging-deploy.yml/badge.svg?branch=staging)](https://github.com/coreblueltd/preconvey/actions/workflows/staging-deploy.yml) [![codecov](https://codecov.io/gh/coreblueltd/preconvey/branch/staging/graph/badge.svg?token=GUUO1RFH1D)](https://codecov.io/gh/coreblueltd/preconvey)

> SaaS for property conveyancing, aimed at simplifying the onboarding and paperwork for conveyancers, buyers, and sellers.

## Project Technologies
- Laravel GraphQL API
- NextJS Typescript frontend with Tailwind
- Flutter mobile app

## Project Structure
This project utilises Yarn Workspaces.
There are 4 workspaces contained within the `packages` directory:
- `app` - The primary frontend web application
- `api` - The backend API
- `mobile` - The mobile app
- `ui` - A collection of reusable React components

## Getting Started
### Backend Setup
1. Clone the repo
2. Duplicate the `.env.example` files in `packages/api` and `packages/app`
3. The environment variables should be self explanatory. Some will require credentials for third party services 
4. Create `packages/api/auth.json` and add credentials for pulling Laravel Nova from Composer:
```
{
  "http-basic": {
    "nova.laravel.com": {
      "username": "email@email.co.uk",
      "password": "password"
    }
  }
}
```
5. Some of the application requirements (DB, Redis, S3) are Dockerised, start them with Docker Compose: `docker compose up -d`
6. You can enter any container by using `docker-compose exec <container> bash`
7. Run `composer install` inside the `php-fpm` container
8. Generate a new key for your `.env` by running `php artisan key:generate` inside the `php-fpm` container
9. Run the migrations using `php artisan migrate` inside the `php-fpm` container
10. At present it's not recommended to to run the full database seeder as some details from third party services might cause issues. Instead, run the form seeder (`php artisan db:seed --class=FormSeeder`) and sign up as a conveyancer user manually. From there you can configure your setup for local development and testing.

### Frontend setup
1. Install the dependencies using Yarn: `yarn install`. Usage of `npm` is not permitted as the application utilises Yarn Workspaces.
2. Start the dev server using `yarn dev`

### Storybook setup
The application has a set of separate UI components that can be accessed using Storybook by running `yarn storybook`

## Testing
The application contains a set of backend feature and unit tests. These can be run from within the `php-fpm` container with `php artisan test`.

## Deployment
- API - Deployed on AWS via Laravel Vapor
- Web App - Deployed statically on CloudFlare Pages
- Mobile App - Available via both App Store Connect and the Play Store Console

Any changes to the `staging` or `production` branches will automatically trigger a GitHub Actions workflow to run the deployment to the respective environment.

## Resources
- [CoreBlue Client Folder](https://drive.google.com/drive/folders/1yZfpL-k1DLpO1Y_-_djguKD2IqTesZte)
- [MVP Designs](https://www.figma.com/file/PC1kZRxWZ2tGakHLMMwgKG/PreConvey-MVP)
- [Email Designs](https://www.figma.com/file/5SfBgDV4Tg0PYA1GffxoTT/Email-Templates)
- [PDF Form Designs](https://www.figma.com/file/ZQq5BzfgWHE2BYFsvBEofI/PreConvey-Forms)
- [Overview of the conveyancing process](https://www.figma.com/file/RNV3XuwJ5gu2wcMnPvKcEn/High-Level-Conveyancing-Process)

### Third Party Services
The application integrates with several third party services and APIs. Sandbox credentials may be required for some integrations to work during local development.
- Stripe
- Yoti Sign
- Yoti IDV
- Royal Mail AddressNow
- Companies House
