CREATE TABLE Persons (
    PersonID int PRIMARY KEY,
    LastName varchar(255),
    FirstName varchar(255),
    Address varchar(255),
    City varchar(255)
);

INSERT INTO Persons (PersonID, LastName, FirstName, Address, City) VALUES
(1, 'Smith', 'John', '123 Elm St', 'New York'),
(2, 'Johnson', 'Emily', '456 Maple Ave', 'Los Angeles'),
(3, 'Williams', 'Michael', '789 Oak Blvd', 'Chicago'),
(4, 'Brown', 'Sarah', '101 Pine Dr', 'Houston'),
(5, 'Jones', 'David', '202 Birch Ct', 'Phoenix'),
(6, 'Garcia', 'Jessica', '303 Cedar Ln', 'Philadelphia'),
(7, 'Martinez', 'Daniel', '404 Walnut Rd', 'San Antonio'),
(8, 'Davis', 'Sophia', '505 Spruce Pl', 'San Diego'),
(9, 'Rodriguez', 'Matthew', '606 Fir St', 'Dallas'),
(10, 'Wilson', 'Olivia', '707 Cypress Way', 'San Jose');