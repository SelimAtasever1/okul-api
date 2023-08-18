## Okul API

Welcome to the Okul API repository. Below are some steps and helpful commands to guide you through the setup and functionality of the API.

## Basic Authentication Setup

To refactor the password for basic authentication, run:

php artisan tinker

(assuming you have a user with email 'john.doe@example.com')

$user = App\Models\User::where('email', 'john.doe@example.com')->first();
$user->password = Hash::make('123123');
$user->save();


## MySQL Event Scheduler

To auto-reject pending preorders older than one day, execute the following MySQL command:

UPDATE preorders 
SET status = 'autoRejected' 
WHERE TIMESTAMPDIFF(DAY, created_at, NOW()) > 1 AND status = 'pending';

(make sure you enabled the event scheduler in your db)

## Twilio Configuration

First, install the Twilio SDK:

composer require twilio/sdk

Update your .env file with the following Twilio configurations:

TWILIO_SID=Your_twilio_id
TWILIO_TOKEN=Your_twilio_token
TWILIO_PHONE=Your_num

(Note: Ensure you replace the placeholders with your actual Twilio credentials.)

## Swagger Documentation Generation

To generate Swagger documentation:

./vendor/bin/openapi --output ./public/docs ./app

## PHPUnit

For unit testing, you may need to install PHPUnit:

composer require --dev phpunit/phpunit

