INSERT INTO Ingredient (name) VALUES ('light rum');
INSERT INTO Ingredient (name) VALUES ('cola');
INSERT INTO Ingredient (name) VALUES ('lime juice');

INSERT INTO Ingredient (name) VALUES ('gin');
INSERT INTO Ingredient (name) VALUES ('dry vermouth');
INSERT INTO Ingredient (name) VALUES ('olive');

INSERT INTO Ingredient (name) VALUES ('white rum');
INSERT INTO Ingredient (name) VALUES ('mint');
INSERT INTO Ingredient (name) VALUES ('sugar');
INSERT INTO Ingredient (name) VALUES ('soda water');

INSERT INTO Category (name) VALUES ('Cocktail');
INSERT INTO Category (name) VALUES ('Shot');

INSERT INTO Recipe (name, category, instructions) VALUES ('Cuba Libre', 1,
	'Squeeze a lime into a Collins glass, add 2 or 3 ice cubes, and pour in the rum. Fill with cold Coca-Cola. Stir briefly.');

INSERT INTO Recipe (name, category, instructions) VALUES ('Dry Martini', 1,
	'Straight: Pour all ingredients into mixing glass with ice cubes. Stir well. Strain in chilled martini cocktail glass. Garnish with olive.');

INSERT INTO Recipe (name, category, instructions) VALUES ('Mojito', 1,
	'Muddle mint leaves with sugar and lime juice. Add a splash of soda water and fill the glass with cracked ice. Pour the rum and top with soda water. Garnish and serve with straw.');

INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (2, 1, '12 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (1, 1, '5 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (3, 1, '1 cl');

INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (4, 2, '6 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (5, 2, '1 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (6, 2, '1');

INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (3, 3, '3 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (7, 3, '4 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (8, 3, '6 sprigs');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (9, 3, '2 tsp');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (10, 3, 'to taste');

INSERT INTO Service_user (name, password) VALUES ('Kalle', 'Kalle123');
INSERT INTO Service_user (name, password) VALUES ('name', 'password');



