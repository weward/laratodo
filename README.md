# LaraTODO


```
Author: Roland Santos
Email: dev.weward@gmail.com
GitHub: https://github.com/weward
```

# Table Of contents

- [Installation - API](#installation---api)
- [Installation - Frontend](#installation---frontend)
- [User Credentials](#user-credentials)
- [Notes](#notes)
- [API Documentation](#api-documentation)
- [Features](#features)


---
---
---


## Installation - API

- Clone the Repo

```sh
git clone <repo-link>
```
- Go to API directory

```
# API

cd api 

```

- Install dependencies

```sh
composer install
```


- Update .ENV values

- Generate Security Key

```sh
php artisan key:generate
```



- Migrate the Database

This was developed using MySql

```sh
php artisan migrate 
```

- **MUST** - Seed the database

```sh
php artisan db:seed 
```

- Run the tests

```sh
php artisan test 
```

## Installation - Frontend

- Go to FRONTEND directory

---

- Compatibility

```
"node": "^18 || ^16 || ^14.19",
"npm": ">= 6.13.4",
"yarn": ">= 1.21.1"
```

## Root -> Frontend

cd frontend

- Set `.env` variables

> Set the `API` url in the `VITE_API_URL` variable in the `.env` file at `./frontend`.

---

- Install the dependencies

```
npm install
```

- Start the app in dev mode

```
npm run dev
```

### User Credentials

To login: 
```
Email: admin@admin.com
Password: password
```


---

## Notes

- This was created using:
    - Api - Laravel v8
    - Front-end: 
        - Vue 3
        - Pinia
        - Vite
        - Quasar Vue Framework
        - TailwindCss

- A Job called `DeleteArchivedTasksJob` handles the deletion of tasks which sits on the archive for more than a week.
- A Job called `RemoveFileFromStorageJob` gets added to the queue to processes the *asynchronous* deletion of files from the storage whenever an attachment gets removed. 
- Multiple tests were setup to cover the requirements stated in the provided coding challenge document.

---

## API Documentation
- Documentation is available at [PostMan](https://documenter.getpostman.com/view/6440758/2s9Y5cug1f)



## FEATURES

- Basic Authentication
- User Registration
- Task Management 
    - Create task
    - Update task
    - View task
    - Delete task
    - Mark task as completed
    - Mark task as todo
    - Mark task as archived
    - Restore task from archives
    - Set due date
    - Set task priority
    - Add tags 
    - Upload attachments (Image/Video/Documents)
    - Download attachments
- Search Tasks
    - By title / desccription
    - By priority
    - By due date
    - By completion date
    - By archived date
    - Sort by (ascending / descending): 
        - title
        - description
        - due date
        - date created
        - date completed
        - priority
- Pagination
- Card style
- Admin Panel with sidebar
