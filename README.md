# Spareinkauf B2B Fax Management API

## 1. Project Summary
Spareinkauf B2B Fax Management is a Laravel-based backend application designed to digitize and structure legacy vendor transmissions. It acts as an integration gateway between legacy analog order systems and modern data flow pipelines, routing procurement requests seamlessly into target databases.

## 2. Business Problem
Traditional B2B operations often experience integration bottlenecks caused by legacy fax and email order handling. This application modernizes the pipeline by providing an automated abstraction layer, decreasing order-execution latency and minimizing manual intervention.

## 3. Tech Stack
- **Backend:** PHP 8.x, Laravel Framework
- **Database:** MySQL
- **Architecture:** API Routes, MVC, Service Controllers

## 4. Architecture Overview
The application functions as a localized integration point. It ingests legacy B2B order formats, sanitizes them through strict request validation logic, and maps them to external API systems (including direct REST bridges).

## 5. API / Backend Flow
Endpoints declared under `routes/api.php` filter inbound requests through validation rules, utilizing dedicated Laravel services to execute the cross-communication. Order telemetry is normalized and passed to downstream database structures.

## 6. Database Structure
Powered by Laravel Eloquent ORM. Migration-based schemas guarantee repeatable deployment. Handles local telemetry persistence alongside dual-database connections for distinct read/write paths required by external application sync streams.

## 7. Validation and Error Handling
Leverages Laravel Form Requests and Controller-level validation blocks. Ensures malformed transmission packets are rejected with clear HTTP response codes (e.g., `422 Unprocessable Entity`), providing visibility back to the client interface.

## 8. Local Setup
Ensure you have PHP 8 and Composer installed localized to your development container.

\`\`\`bash
git clone https://github.com/kaptantr/spareinkauf-fax-management.git
cd spareinkauf-fax-management
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
\`\`\`

## 9. Testing
Built upon a PHPUnit testing foundation. Integrates unit and feature testing for validating expected API mapping, guaranteeing robust handling during regression phases.

## 10. What This Demonstrates Professionally
This project provides proof of capability in managing and scaling enterprise-style backend integration concepts using modern framework conventions. It explicitly highlights PHP Laravel ecosystem mastery, decoupled system bridging, and secure API structure logic.
