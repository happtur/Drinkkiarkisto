CREATE TABLE Ingredient(
	id SERIAL PRIMARY KEY,
	name varchar(50) UNIQUE NOT NULL
);

CREATE TABLE Category(
	id SERIAL PRIMARY KEY,
	name varchar(50) UNIQUE NOT NULL
);

CREATE TABLE Recipe(
	id SERIAL PRIMARY KEY,
	name varchar(50) UNIQUE NOT NULL,
	category INTEGER REFERENCES Category(id),
	instructions varchar(600),
	approved boolean NOT NULL,
	added_by INTEGER REFERENCES Service_user(id)
);

CREATE TABLE Recipe_ingredient(
	ingredient INTEGER REFERENCES Ingredient(id),
	recipe INTEGER REFERENCES Recipe(id),
	amount varchar(30) NOT NULL
);

CREATE TABLE Service_user(
	id SERIAL PRIMARY KEY,
	name varchar(50) UNIQUE NOT NULL,
	password varchar(50) NOT NULL,
	admin boolean DEFAULT false
);
