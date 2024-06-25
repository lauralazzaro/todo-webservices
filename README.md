# ToDo & Co

[![Symfony](https://github.com/lauralazzaro/projet8-TodoList/actions/workflows/symfony.yml/badge.svg)](https://github.com/lauralazzaro/projet8-TodoList/actions/workflows/symfony.yml)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/e75c4044c8cb4aebbb72c6d8e07cbc13)](https://app.codacy.com/gh/lauralazzaro/projet8-TodoList/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

## Description

Project 8 - OpenClassrooms PHP/Symfony Developer

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Database Setup](#database-setup)
- [Contribution](contributions.md)

### Prerequisites

Make sure you have the following software installed on your machine:

- PHP (>=8.2)
- Composer
- Node.js and npm
- SQLite
- Symfony CLI

### Installation

- **Clone the repository:**

  Choose the URL of preference from the code page of the repository.

- **Create `.env.local` and set up the file with these variables:**

   ```bash
  APP_ENV=dev
  APP_SECRET=use-your-own-secret-key
  MAILER_DSN=smtp://your-email:your-password@smpt-server
  MAILER_TO=[use-your-email-to-intercept-signup-mail]
  MAILER_FROM=[use-your-email]
  ```
- **Create `.env.test` and set up the file with these variables:**

  ```bash
  APP_ENV=test
  APP_SECRET=use-your-own-secret-key
  DATABASE_URL=sqlite:///%kernel.project_dir%/var/test.db
  MAILER_DSN=smtp://your-email:your-password@smpt-server
  MAILER_TO=[use-your-email-to-intercept-signup-mail]
  MAILER_FROM=[use-your-email]
  BOOTSTRAP_FIXTURES_LOAD=true
  ```

- **Install PHP dependencies:**

   ```bash
   composer install
   ```

- **Install JavaScript dependencies:**

  ```bash
  npm install
  ```

- **Build assets for dev environment:**

   ```bash
   npm run dev
   ```

### Database Setup

- Create the database with:

    ```bash
    bin/console doctrine:database:create
    ```

- Create the tables with:

    ```bash
    bin/console doctrine:schema:update --force --complete
    ```

### Usage

To start the Symfony development server, run:

   ```bash
   symfony server:start
   ```

### Contributions

See [contributions.md](contributions.md) for information
