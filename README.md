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

```
cd api
```

[API Installation Instructions](https://github.com/weward/laratodo/blob/master/api/README.md)

## Installation - Frontend

```
cd frontend
```

[Frontend Installation Instructions](https://github.com/weward/laratodo/blob/master/frontend/README.md)

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
- Documentation is available in [Postman](https://documenter.getpostman.com/view/6440758/2s9Y5cug1f)



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
