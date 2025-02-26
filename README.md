# Airport Arrival Flights

This project is a PHP-based web application that processes and displays arrival flight data. It converts flight data from an Excel file into XML and JSON formats, populates a MySQL database with the data, and then provides an HTML interface to view and filter flight details. The application also demonstrates various data conversions, table rendering, and filtering based on criteria such as departure city, flight status, and schedule time.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [Usage](#usage)
- [File Structure](#file-structure)
- [Configuration](#configuration)
- [License](#license)
- [Acknowledgements](#acknowledgements)

## Overview

The **Airport Arrival Flights** application performs the following key tasks:
- **Excel to XML Conversion:** Uses the PhpSpreadsheet library to convert data from an `arrivals.xls` file into an XML format.
- **XML to JSON Conversion:** Converts the generated XML into a JSON file (`arrivals.json`).
- **HTML Table Rendering:** Reads the JSON file and displays the flight data in a styled HTML table.
- **Database Integration:** Populates a MySQL database with arrival flight details.
- **Data Filtering:** Provides options to filter flights by city, display flights that have landed, or filter flights scheduled after a specified time.

## Features

- **Data Conversion:** 
  - XLS → XML conversion with date/time formatting.
  - XML → JSON conversion with pretty-printing.
- **Dynamic HTML Rendering:** Displays flight information in a responsive and styled HTML table using JavaScript.
- **Database Operations:**
  - Creates an `arrivals` table if it does not exist.
  - Inserts flight data into the database after checking for duplicates.
  - Supports filtering of flight data based on user input.
- **Filtering Options:**
  - **Filter By City:** Group flights by their originating city.
  - **Landed Flights:** Display only flights that have landed.
  - **Scheduled Time Filter:** Show flights scheduled after a user-specified time.

## Technologies Used

- **Backend:** PHP
- **Frontend:** HTML, CSS, JavaScript (fetch API)
- **Libraries:**
  - [PhpSpreadsheet](https://phpspreadsheet.readthedocs.io/) for processing Excel files.
  - PHP's SimpleXML for XML processing.
- **Database:** MySQL
- **Dependency Management:** Composer

## Installation

1. **Clone the Repository:**

   ```bash
   git clone https://github.com/OmarSalemDMET/Airport_Arrival_Flights.git
   cd Airport_Arrival_Flights
   ```

2. **Install PHP Dependencies:**

   Make sure [Composer](https://getcomposer.org/) is installed, then run:

   ```bash
   composer install
   ```

3. **Set Up the Database:**

   - Create a MySQL database (e.g., `FLIGHTSDB`).
   - Update the database credentials in the `database.php` file if necessary.

4. **Place the Excel File:**

   Ensure that your `arrivals.xls` file is located in the root directory of the project.

5. **Deploy the Application:**

   - Place the project files in your web server's root directory (e.g., Apache’s `htdocs`).
   - Access the application via your browser (e.g., `http://localhost/Airport_Arrival_Flights/main.php`).

## Usage

When you open `main.php` in your browser, the following operations occur:

1. **File Conversions:**
   - The application converts `arrivals.xls` to `arrivals.xml`.
   - The XML file is then converted to `arrivals.json`.
2. **HTML Rendering:**
   - The JSON data is fetched by JavaScript and rendered as an HTML table with custom styles.
3. **Database Operations:**
   - The flight data from the JSON file is inserted into the MySQL `arrivals` table.
4. **Filtering:**
   - Use the provided form buttons to:
     - **Filter By City:** Group flights by origin.
     - **Landed Flights:** Show only flights with a status of "Landed".
     - **Filter by Time:** Input a schedule time to view flights scheduled after that time.

## File Structure

```
Airport_Arrival_Flights/
├── main.php             # Main application file handling data conversion, display, and filtering.
├── database.php         # Database connection script.
├── arrivals.xls         # Source Excel file containing arrival flight data.
├── arrivals.xml         # Generated XML file (output of conversion).
├── arrivals.json        # Generated JSON file (output of conversion).
├── vendor/              # Composer dependencies (including PhpSpreadsheet).
├── README.md            # Project documentation.
└── [Other Assets]       # Additional files such as CSS, JavaScript (if any), etc.
```

## Configuration

- **Database Credentials:**  
  Update the following in `database.php` if needed:
  ```php
  $db_server = "localhost";  
  $db_user = "root";         
  $db_pass = "password";             
  $db_name = 'FLIGHTSDB';
  ```
- **Excel File:**  
  Ensure that `arrivals.xls` is present in the project root for the conversion functions to work properly.

## License

*This project is currently not licensed.*  
If you wish to apply an open-source license (e.g., MIT License), add a LICENSE file to the repository.

## Acknowledgements

- **PhpSpreadsheet:** For powerful Excel file processing capabilities.
- **SimpleXML:** For XML processing in PHP.
- **MySQL:** For reliable database management.
- Thanks to all contributors and maintainers of the libraries and tools used in this project.
