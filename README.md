Project Overview: Automated API & Data Integrity Suite

The Challenge:
To design and implement a backend solution for a Czech Name Day API that handles complex data relationships (Names & Contacts) while ensuring 100% data reliability across the entire calendar year.

Technical Execution:
Database Architecture: Leveraged PostgreSQL with advanced JSON functions (JSON_AGG, JSON_BUILD_OBJECT) to aggregate data directly at the database level, significantly reducing API latency and PHP processing overhead.


Backend Logic: Developed a PHP 8+ API using PDO for secure database interaction, featuring a custom transformation engine to map human-readable dates (DD.MM.) to ISO-standard database keys.

API Documentation: Defined the entire interface using OpenAPI 3.0 (Swagger), ensuring clear contracts for frontend integration.

Quality Assurance & Automation (The "JMeter" Factor):
To guarantee system stability, I implemented a Data-Driven Testing (DDT) suite in Apache JMeter:

Comprehensive Coverage: Automated verification of 366 unique endpoints (covering every day of the year, including leap years) via external CSV datasets.

Structural Validation: Integrated JSONPath Assertions to verify the integrity of the response schema, ensuring that optional relations (Contacts) always return valid JSON structures.

Resilience Testing: Validated error handling and HTTP status codes (404/500) for edge-case scenarios and malformed requests.

Key Result:
A production-ready, fully automated API environment that guarantees data accuracy and high performance, documented and tested according to industry standards for Enterprise Architecture.
