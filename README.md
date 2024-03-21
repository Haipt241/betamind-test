## Get Started

This guide will walk you through the steps needed to get this project up and running on your local machine.

### Prerequisites

Before you begin, ensure you have the following installed:

- Docker
- Docker Compose

### Building the Docker Environment

Build and start the containers:

```
docker-compose up -d --build
```

### Installing Dependencies

```
docker-compose exec app sh
composer install
```

### Database Setup

Set up the database:

```
bin/cake migrations migrate
```

### Accessing the Application

The application should now be accessible at http://localhost:34251

## How to check

### Authentication

- Access the login interface at http://localhost:34251/user/login.
- If you do not have an account, click on "Add User" to create a new account.
- To logout, visit the URL http://localhost:34251/user/logout.

### Article Management

- The features can be directly used at http://localhost:34251/articles. You may try accessing it both logged in and not logged in.
- You can also use Postman for operations. To work with session-based authentication in Postman, follow these steps after logging in through the browser and obtaining the session cookie information:
  - **Login via the browser** at `http://localhost:34251/user/login` and capture the session cookies information.
  - **Configure Postman with the session cookie**:
      - In Postman, navigate to the Headers section of your request. Refer to [Managing cookies in Postman](https://learning.postman.com/docs/sending-requests/cookies/) for detailed instructions.

  - **Set up your request in Postman as required**, for example, to Create an Article (POST):
      - Configure the URL to `http://localhost:34251/articles.json`
      - Method: `POST`
      - Body: Use the raw JSON format with the content:
          ```json
          {
            "title": "Example Title",
            "body": "This is the body of the article."
          }
          ```
      - Headers: Configure the session cookie as instructed above.

This approach allows you to simulate an authenticated session in Postman, enabling you to test and interact with your web application's server-side functionalities that require user authentication.


### Like Feature

- A Likes counter and a like button have been added at http://localhost:34251/articles. Try it out both when logged in and not logged in.
