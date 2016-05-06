INSERT INTO Service_user (name, password) VALUES ('Kalle', 'Kalle123');
INSERT INTO Service_user (name, password) VALUES ('name', 'password');
INSERT INTO Service_user (name, password, admin) VALUES ('admin', 'password', true);
INSERT INTO Service_user (name, password, admin) VALUES ('Pelle', 'Pelle123', true);

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

INSERT INTO Ingredient (name) VALUES ('vodka');
INSERT INTO Ingredient (name) VALUES ('lemon juice');

INSERT INTO Ingredient (name) VALUES ('white wine');
INSERT INTO Ingredient (name) VALUES ('lime');
INSERT INTO Ingredient (name) VALUES ('lemon');

INSERT INTO Ingredient (name) VALUES ('water');
INSERT INTO Ingredient (name) VALUES ('brown sugar (refined sugar to which molasses has been added)');
INSERT INTO Ingredient (name) VALUES ('fresh yeast');
INSERT INTO Ingredient (name) VALUES ('raisins');

INSERT INTO Ingredient (name) VALUES ('cranberry juice');
INSERT INTO Ingredient (name) VALUES ('pink grapefruit juice');
INSERT INTO Ingredient (name) VALUES ('sparkling wine');
INSERT INTO Ingredient (name) VALUES ('grapefruit');

INSERT INTO Ingredient (name) VALUES ('naughty word');
INSERT INTO Ingredient (name) VALUES ('bad language');

INSERT INTO Category (name) VALUES ('Cocktail');
INSERT INTO Category (name) VALUES ('Shot');
INSERT INTO Category (name) VALUES ('Punch');
INSERT INTO Category (name) VALUES ('Non-alcoholic');
INSERT INTO Category (name) VALUES ('Hot beverage');
INSERT INTO Category (name) VALUES ('Other');

INSERT INTO Recipe (name, category, approved, added_by, instructions) VALUES ('Sparkling Seabreeze Punch', 3, false, 1,
	'Combine the cranberry juice, grapefruit juice and vodka in a pitcher or punch bowl. Gently stir in sparkling wine. Garnish with grapefruit wheels or wedges, if desired.');

INSERT INTO Recipe (name, category, approved, added_by, instructions) VALUES ('Dry Martini', 1, true, 3,
	'Straight: Pour all ingredients into mixing glass with ice cubes. Stir well. Strain in chilled martini cocktail glass. Garnish with olive.');

INSERT INTO Recipe (name, category, approved, added_by, instructions) VALUES ('Mojito', 1, true, 2,
	'Muddle mint leaves with sugar and lime juice. Add a splash of soda water and fill the glass with cracked ice. Pour the rum and top with soda water. Garnish and serve with straw.');

INSERT INTO Recipe (name, category, approved, added_by, instructions) VALUES ('Lemon drop', 2, true, 2,
	'Add sugar to the rim of your glass. In a cocktail shaker fill with ice pour in your Vokda, Lemon juice and sugar. Shake until chilled, and strain into the shot glass.');

INSERT INTO Recipe (name, category, approved, added_by, instructions) VALUES ('Mint and Citrus White Wine Sangria', 3, true, 1,
	'Prepare simple syrup by mixing 2 Tablespoons of sugar and 2 Tablespoons of water in a small dish and microwaving for 30-second increments until sugar is dissolved.
	Once simple syrup is ready, add 1 Tablespoon to each wine glass. Then add mint leaves and stir/lightly muddle.
	Next add several thin slices each of lemon and lime.
	Top off with wine and let set for a few minutes so the flavors can meld together. Top off with more wine and citrus as needed.
');

INSERT INTO Recipe (name, category, approved, added_by, instructions) VALUES ('Sima', 6, true, 4,
	'Boil half of the water and pour over the sugars. Add the rest of the water and the lemon juice (for extra flavour add the peel (remove it in the bottling phase)), let cool to about 35-40°C. Dissolve the yeast in a small amount of the liquid and then mix into the rest. Keep at room temperature for a day before bottling. Add 1 tsp of sugar and a few raisins to the bottles. Do not put the cap on too tightly. The sima is ready when the raisins rise to the surface (about a week at fridge temperature and three days at room temperature). Consume within a week, store in the fridge.');

INSERT INTO Recipe (name, category, approved, added_by, instructions) VALUES ('Cuba Libre', 1, false, 1,
	'Squeeze a lime into a Collins glass, add 2 or 3 ice cubes, and pour in the rum. Fill with cold Coca-Cola. Stir briefly.');


INSERT INTO Recipe (name, category, approved, instructions) VALUES ('Excrement juice (spam)', 5, false,
	'Shocking content');

INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (2, 7, '12 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (1, 7, '5 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (3, 7, '1 cl');

INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (4, 2, '6 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (5, 2, '1 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (6, 2, '1');

INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (3, 3, '3 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (7, 3, '4 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (8, 3, '6 sprigs');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (9, 3, '2 tsp');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (10, 3, 'to taste');

INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (11, 4, '4 cl');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (12, 4, '1 tbsp');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (9, 4, '2 tsp');

INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (13, 5, '1 bottle');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (14, 5, '1');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (15, 5, '1');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (8, 5, '8-10 leaves');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (9, 5, '2 tbsp');

INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (16, 6, '4 l');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (17, 6, '250 g');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (9, 6, '250 g');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (15, 6, '1-2');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (18, 6, '1 ml');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (19, 6, 'a few');

INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (20, 1, '2 cups');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (21, 1, '2 cups');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (11, 1, '1 cup');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (22, 1, '750 ml');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (23, 1, '1-2');

INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (24, 8, 'A disgusting amount');
INSERT INTO Recipe_ingredient (ingredient, recipe, amount) VALUES (25, 8, 'A metric fuckton');