INSERT INTO Ingredient (name) VALUES ('light rum');
INSERT INTO Ingredient (name) VALUES ('cola');
INSERT INTO Ingredient (name) VALUES ('lime juice');

INSERT INTO Ingredient (name) VALUES ('gin');
INSERT INTO Ingredient (name) VALUES ('dry vermouth');
INSERT INTO Ingredient (name) VALUES ('olive');

INSERT INTO Category (name) VALUES ('Cocktail');
INSERT INTO Category (name) VALUES ('Shot');

INSERT INTO Recipe (name, category, instructions) VALUES ('Cuba Libre', 1,
	'Squeeze a lime into a Collins glass, add 2 or 3 ice cubes, and pour in
	 the rum. Fill with cold Coca-Cola. Stir briefly.');

INSERT INTO Recipe (name, category, instructions) VALUES ('Dry Martini', 1,
	'Straight: Pour all ingredients into mixing glass with ice cubes. 
	Stir well. Strain in chilled martini cocktail glass. Garnish with olive.');

INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (2, 1, '12 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (1, 1, '5 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (3, 1, '1 cl');

INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (4, 2, '6 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (5, 2, '1 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (6, 2, '1');

INSERT INTO Service_user (name, password) VALUES ('Kalle', 'Kalle123');


