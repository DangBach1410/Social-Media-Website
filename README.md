# Course Help Hub

Course Help Hub is a web-based platform designed to facilitate interaction among students for coursework-related discussions. Students can post questions, provide answers, comment on posts, and manage their content. The platform includes features like user authentication, profile management, content management, and an advanced search function.

## Table of Contents
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [Usage](#usage)
- [Testing](#testing)
- [Contributing](#contributing)

## Features

- **User Authentication**: Allows users to register, log in, and manage their profiles.
- **Question and Comment Management**: Users can post questions, comment on them, and edit or delete their content.
- **Profile Management**: Users can view and edit their profiles, change passwords, and delete accounts.
- **Module Management (Admin Only)**: Administrators can manage modules (add, edit, delete).
- **Search Functionality**: Search for questions, comments, users, and modules with filtering options.
- **Responsive Design**: The platform is fully responsive, providing an optimal experience on desktop, tablet, and mobile devices.

## Technologies Used

- **PHP**: Server-side scripting language used for implementing business logic.
- **MySQL**: Database management system to store user data, questions, comments, and modules.
- **HTML5**: For structuring web pages with semantic elements.
- **CSS3**: For styling the web pages and ensuring a responsive design.
- **JavaScript**: To enhance client-side interactivity and validate form inputs.
- **PDO (PHP Data Objects)**: Used to interact with the MySQL database securely and efficiently, preventing SQL injection.

## Installation

### Prerequisites
- PHP (version 7.4 or higher)
- MySQL
- A local server like XAMPP, WAMP, or MAMP

### Steps
1. Clone the repository to your local machine:
   ```bash
   git clone https://github.com/your-username/course-help-hub.git
   ```
2. Set up a local server (e.g., XAMPP).
3. Import the database schema provided in the database.sql file into your MySQL database.
4. Configure the database connection
5. Start the server and open the project in your browser by navigating to http://localhost/course-help-hub

## Usage

1. **Login/Sign Up**: 
   - Register as a new user or log in with your existing credentials.
   - After logging in, you will have access to the platform’s features.
   
2. **Post Questions**: 
   - Once logged in, you can post questions to the platform by selecting the relevant module.
   - To post a question, navigate to the "Post a Question" page, fill in the required details, and submit.

3. **Comment on Posts**: 
   - Engage with other posts by leaving comments.
   - Simply click on a post to view it and add your comments. You can also reply to other comments.

4. **Manage Profile**: 
   - You can update your profile, change your password, and even delete your account.
   - Navigate to the "Profile" page to make changes to your personal information and settings.

5. **Admin Access**: 
   - Administrators can manage the modules (add, edit, delete).
   - Admins have access to a dedicated "Manage Modules" page, where they can perform these actions.

## Testing

The platform has been thoroughly tested with over 50 test cases to ensure its functionality. Key features like login, registration, content management, and search have been validated to ensure smooth operation.

- **Key Testing Areas**:
  - **Login/Sign Up**: Ensures that user authentication functions properly for both regular users and administrators.
  - **Post Questions**: Confirms that users can post, edit, and delete questions successfully.
  - **Comment System**: Verifies that users can comment on posts and manage their comments.
  - **Search**: Ensures that the search bar returns accurate results for questions, users, and modules.
  
For details on the test cases and outcomes, please refer to the `test_log.txt` file in the repository.

## Contributing

I welcome contributions to the Course Help Hub project! If you’d like to improve the platform or add new features, please follow these steps:

1. **Fork the repository**:
   - Create a copy of the repository under your GitHub account.
   - Click the "Fork" button in the upper-right corner of the repository page.

2. **Create a new branch for your feature**:
   - Navigate to your forked repository and create a new branch:
     ```bash
     git checkout -b feature-name
     ```

3. **Make your changes and commit them**:
   - Make changes to the codebase and add your commits:
     ```bash
     git commit -m 'Add new feature'
     ```

4. **Push to your fork**:
   - Push your changes to your remote fork:
     ```bash
     git push origin feature-name
     ```

5. **Create a pull request**:
   - Go to the "Pull Requests" section of the original repository and click on "New Pull Request".
   - Select your branch and submit the pull request to merge your changes into the main repository.

Please ensure your code adheres to the existing style and passes all test cases. I appreciate your contributions and look forward to seeing how you can improve the platform!
