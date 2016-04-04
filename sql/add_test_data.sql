INSERT INTO Ingredient (name) VALUES ('light rum');
INSERT INTO Ingredient (name) VALUES ('cola');
INSERT INTO Ingredient (name) VALUES ('lime juice');

INSERT INTO Category (name) VALUES ('Cocktail');
INSERT INTO Category (name) VALUES ('Shot');

INSERT INTO Recipe (name, category, instructions) VALUES ('Cuba Libre', 1,
	'Squeeze a lime into a Collins glass, add 2 or 3 ice cubes, and pour in
	 the rum. Fill with cold Coca-Cola. Stir briefly.');

INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (2, 1, '12 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (1, 1, '5 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (3, 1, '1 cl');

INSERT INTO Service_user (name, password) VALUES ('Kalle', 'Kalle123');


