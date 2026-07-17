-- ===========================================================
-- ADD DESCRIPTION COLUMN TO FOODS TABLE
-- ===========================================================
-- Run this in phpMyAdmin (SQL tab) on your "project" database.
-- Step 1: Adds the description column
-- Step 2: Updates every food item with its existing description
--         taken from the category pages (pizza.php, burger.php etc.)
-- ===========================================================

-- STEP 1: Add description column to the foods table
ALTER TABLE foods
ADD COLUMN description VARCHAR(500) DEFAULT NULL AFTER f_name;


-- STEP 2: Update every food with its existing description
-- (copy-pasted from the category PHP pages so they match exactly)

-- BURGERS (IDs 1-7)
UPDATE foods SET description = 'A classic cheeseburger featuring a juicy beef patty, melted cheese, and fresh toppings. Served on a warm, toasted bun for the perfect bite every time.' WHERE f_id = 1;
UPDATE foods SET description = 'A bold BBQ burger loaded with smoky sauce, melted cheese, and crispy onion rings. Grilled to perfection and packed with sweet, savory flavor in every bite.' WHERE f_id = 2;
UPDATE foods SET description = 'A juicy, seasoned chicken patty paired with crisp lettuce and creamy sauce. Served on a soft, toasted bun for a deliciously satisfying bite.' WHERE f_id = 3;
UPDATE foods SET description = 'A crispy, golden fish fillet with tender flakes inside, topped with tartar sauce and fresh lettuce. Served in a soft bun for a light yet flavorful seafood delight.' WHERE f_id = 4;
UPDATE foods SET description = 'A juicy, protein-packed burger with cheese, veggies, and savory toppings, wrapped in crisp lettuce or a keto bun. Perfect for low-carb lifestyles without sacrificing flavor.' WHERE f_id = 5;
UPDATE foods SET description = 'A flavorful paneer burger with a spiced, grilled cottage cheese patty and zesty chutneys. Layered with fresh veggies and served in a soft, toasted bun for a tasty vegetarian treat.' WHERE f_id = 6;
UPDATE foods SET description = 'A wholesome veg burger featuring a tasty patty made from mixed vegetables, grains, or legumes. Topped with fresh veggies and sauces, all tucked into a soft, toasted bun.' WHERE f_id = 7;

-- PIZZAS (IDs 8-13)
UPDATE foods SET description = 'A classic favourite, topped with melted cheese, zesty tomato sauce, and savory Pepperoni slices on a perfectly baked crust.' WHERE f_id = 8;
UPDATE foods SET description = 'Features a soft, savory dough topped with tender chicken and rich, zesty sauce. Melted cheese adds a creamy finish to every delicious bite.' WHERE f_id = 9;
UPDATE foods SET description = 'A carnivore\'s dream, piled with savory meats like Pepperoni, sausage, ham, and bacon on a cheesy base.' WHERE f_id = 10;
UPDATE foods SET description = 'Offers a rich, earthy flavor with a generous topping of fresh mushrooms. Melted cheese and a crispy crust complete this savory delight.' WHERE f_id = 11;
UPDATE foods SET description = 'Loaded with a colorful variety of fresh vegetables and rich tomato sauce. Topped with gooey cheese on a soft yet crispy crust, it\'s a perfect vegetarian treat.' WHERE f_id = 12;
UPDATE foods SET description = 'Blends sweet pineapple chunks with savory toppings for a bold flavor contrast. Served on a cheesy base, it\'s a sweet-and-salty twist on the classic pizza.' WHERE f_id = 13;

-- FRIED CHICKEN (IDs 14-19)
UPDATE foods SET description = 'Golden-brown chicken fried to crispy perfection. Juicy and tender inside with a crunchy coating. Served hot with ketchup or garlic dip.' WHERE f_id = 14;
UPDATE foods SET description = 'Crispy wings tossed in bold spicy sauce. Perfect balance of heat and flavor. Best choice for spice lovers.' WHERE f_id = 15;
UPDATE foods SET description = 'Thick and meaty drumsticks deep-fried fresh. Seasoned with special herbs and spices. Crispy outside and juicy inside.' WHERE f_id = 16;
UPDATE foods SET description = 'Bite-sized crispy chicken pieces. Light, crunchy and easy to share. Perfect snack for any time hunger.' WHERE f_id = 17;
UPDATE foods SET description = 'Boneless chicken strips coated in seasoned flour. Deep-fried until golden and crunchy. Served with special creamy dip.' WHERE f_id = 18;
UPDATE foods SET description = 'A large bucket of assorted crispy chicken. Perfect for family and group meals. Comes with multiple dipping sauces.' WHERE f_id = 19;

-- PASTA (IDs 20-25)
UPDATE foods SET description = 'Penne pasta in rich white cream sauce. Flavored with herbs and parmesan cheese. Smooth, creamy and delicious.' WHERE f_id = 20;
UPDATE foods SET description = 'Classic spaghetti with minced chicken sauce. Slow-cooked tomato base with spices. Hearty and full of flavor.' WHERE f_id = 21;
UPDATE foods SET description = 'Pasta tossed in tangy tomato sauce. Mixed with capsicum and fresh vegetables. Light, fresh and flavorful.' WHERE f_id = 22;
UPDATE foods SET description = 'Creamy white sauce with sautéed mushrooms. Blended with herbs and cheese. Rich taste for mushroom lovers.' WHERE f_id = 23;
UPDATE foods SET description = 'Fresh seasonal vegetables and pasta. Cooked in light tomato herb sauce. Healthy and tasty option.' WHERE f_id = 24;
UPDATE foods SET description = 'Oven-baked pasta topped with mozzarella. Cheese melts perfectly over rich sauce. Warm, creamy and satisfying.' WHERE f_id = 25;

-- MOMO (IDs 26-32)
UPDATE foods SET description = 'Soft dumplings filled with juicy chicken. Steamed fresh and served hot. Comes with spicy tomato achar.' WHERE f_id = 26;
UPDATE foods SET description = 'Traditional Nepali buffalo momo. Well-seasoned and flavorful filling. Served with homemade spicy sauce.' WHERE f_id = 27;
UPDATE foods SET description = 'Crispy on the outside with a juicy, flavorful filling inside. Each bite offers a satisfying crunch and rich taste.' WHERE f_id = 28;
UPDATE foods SET description = 'Crispy fried momos tossed in a spicy, tangy sauce with onions and capsicum for an extra crunch. Bold, flavorful, and perfectly spicy!' WHERE f_id = 29;
UPDATE foods SET description = 'Stuffed with cabbage and carrot mix. Lightly seasoned with fresh spices. Healthy and delicious option.' WHERE f_id = 30;
UPDATE foods SET description = 'Filled with soft melted cheese. Creamy texture inside dumpling wrap. Perfect for cheese lovers.' WHERE f_id = 31;
UPDATE foods SET description = 'Steamed momo served in spicy soup. Sesame and tomato based gravy. Warm, juicy and comforting.' WHERE f_id = 32;

-- COLD DRINKS (IDs 33-36)
UPDATE foods SET description = 'A popular carbonated soft drink with a refreshing, fizzy taste. Best enjoyed ice cold for a cool, flavorful experience.' WHERE f_id = 33;
UPDATE foods SET description = 'A lemon-lime flavored soda with a light, crisp taste. A refreshing drink that quickly quenches your thirst.' WHERE f_id = 34;
UPDATE foods SET description = 'A chilled cola with a sweet, refreshing taste that complements any meal. Its classic flavor makes it a perfect beverage choice for any occasion.' WHERE f_id = 35;
UPDATE foods SET description = 'A bright, citrus-flavored soft drink bursting with orange taste. Sweet, fizzy and refreshing for any time of day.' WHERE f_id = 36;
