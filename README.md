PHP_Pagination
==============

This class can generate links to browse MySQL query result pages.

It takes the total number of items in a query result and the limit of items to display per page to generate HTML links to browse the different pages of the results listing based on the current page number retrieved from a request parameter.

The class can also generate the MySQL query limit clause to retrieve the results for the current page.

The base URL for the links and the limit of links to appear before and after the current page can be configured.
