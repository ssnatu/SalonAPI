# Task
Develop an API endpoint which accepts an UK postcode and returns the address and opening times of the closest Beauty Salon Location

## Brief
Build an API endpoint to find a nearest Beauty Salon location from given 16 existing Beauty Salon locations and return its address and opening times.

Locations are saved in "location_data.csv" file with Postcodes and their opening and closing times

## Uses

- Enter any UK postcode in the input box
- The closest Beauty Salon location's address will get returend with
  its Opening times from the API
- Bug - Sometimes Google Maps API doesn't respond in time and gives OVER_QUERY_LIMIT ERROR message
- If error occurs, retry few times or retry after some time 

 
## Tools and Technologies
 - PHP, OOP PHP, HTML, CSS, Bootstrap 3.3
 - Postcodes.io API
 - Google Maps API

**Note** Sometimes Google Maps API doesn't respond in time and gives OVER_QUERY_LIMIT ERROR message
If error occurs, retry few times or retry after some time 
