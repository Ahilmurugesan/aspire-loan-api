### Clone
`git clone https://github.com/Ahilmurugesan/aspire-loan-api.git`

### Install Dependencies
- `composer install`
- `cp .env.example .env`
- Create a database and configure the name in the `.env` file with the username and password
- Create a database for unit tests and configure in the phpunit.xml file
  ```php
    <env name="DB_DATABASE" value=":databaseName:"/> // for example value="aspire_loan_Test"

- To create the admin user and 10 other users. Kindly run the below command
   ```console
    php artisan migrate:fresh --seed
  ```
  - Admin credentials =>  admin@mail.com / password
  
### Implementation
- **Authentication**
  - Login (Used by Customer and Admin)
  - Logout (Used by Customer and Admin)
  - Register

- **Loan Request/Proposal**
  - Loan List
    - Available to both admin and customer
    - Customer can see only his/her data whereas admin can view all loans
  - Loan Submission
    - Available only to customer
  - Loan Status Update (Approval/Decline)
    - Available only to user
  -Loan Detail Show
    - Available to both admin and user

- **Repayments**
  - Add Repayments 
    - Available only to customer

- **Unit/Feature Test**
  - Added unit and feature tests for above modules


### Design Pattern
- This projects uses *Laravel Modules* structure with *Service Class* design pattern
- Laravel modules structure will make each module look like a lite weight app
- Inside laravel modules, service class has been used to make the controller to handle only to get the input request and used to send the response message


### Postman Collection
- Added postman collection in the root folder of the project.
- Name of the collect - Aspire API.postman_collection.json
