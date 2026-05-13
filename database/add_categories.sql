-- Add missing categories for inventory system
-- Run this SQL to add 'canister' and 'others' categories

INSERT INTO categories (category_name, description) VALUES 
('canister', 'Canisters and related items'),
('others', 'Other miscellaneous items');
