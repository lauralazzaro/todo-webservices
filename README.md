# ToDo & Co

Project 8 - OpenClassrooms PHP/Symfony Developer

[![Symfony](https://github.com/lauralazzaro/projet8-TodoList/actions/workflows/symfony.yml/badge.svg)](https://github.com/lauralazzaro/projet8-TodoList/actions/workflows/symfony.yml)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/e75c4044c8cb4aebbb72c6d8e07cbc13)](https://app.codacy.com/gh/lauralazzaro/projet8-TodoList/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Database Setup](#database-setup)
- [Contribution](contributions.md)
- [License](#license)

### Prerequisites

Make sure you have the following software installed on your machine:

- PHP (>=8.3)
- Composer
- Node.js and npm
- SQLite
- Symfony (>=7.0)
- Symfony CLI

### Installation

- **Clone the repository:**

Choose the URL of preference from the the code page of the repository.

- **Install PHP dependencies:**

`composer install`

- **Install JavaScript dependencies:**

`npm install`

- **Build assets for dev environment:**

`npm run dev`

- **Set up the env.local file with these variables:**

`APP_ENV=dev`  
`APP_SECRET=05b6455e1c4e4c62ba339f000d1a4544`  
`DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db`  
`MAILER_DSN=smtp://your-email:your-password@smpt-server`

### Database Setup

1. Create the database with:

`
bin/console doctrine:database:create
`

2. Create the tables with:

`
bin/console doctrine:schema:update --force --complete
`

### Usage

To start the Symfony development server, run:

`
symfony server:start
`

### Contributions
See [contributions.md](contributions.md) for information
