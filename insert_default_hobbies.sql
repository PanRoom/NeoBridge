INSERT IGNORE INTO hobby_categories (name) VALUES
('音楽'),
('映画'),
('スポーツ'),
('読書'),
('ゲーム'),
('料理'),
('旅行'),
('その他');

INSERT IGNORE INTO hobby_items (category_id, name) VALUES
((SELECT id FROM hobby_categories WHERE name = '音楽'), '邦楽'),
((SELECT id FROM hobby_categories WHERE name = '音楽'), '洋楽'),
((SELECT id FROM hobby_categories WHERE name = '音楽'), 'K-POP'),
((SELECT id FROM hobby_categories WHERE name = '音楽'), 'J-POP'),
((SELECT id FROM hobby_categories WHERE name = '音楽'), 'ロック'),
((SELECT id FROM hobby_categories WHERE name = '音楽'), 'クラシック');

INSERT IGNORE INTO hobby_items (category_id, name) VALUES
((SELECT id FROM hobby_categories WHERE name = '映画'), 'アクション'),
((SELECT id FROM hobby_categories WHERE name = '映画'), 'SF'),
((SELECT id FROM hobby_categories WHERE name = '映画'), 'コメディ'),
((SELECT id FROM hobby_categories WHERE name = '映画'), 'ホラー');

INSERT IGNORE INTO hobby_items (category_id, name) VALUES
((SELECT id FROM hobby_categories WHERE name = 'スポーツ'), 'サッカー'),
((SELECT id FROM hobby_categories WHERE name = 'スポーツ'), '野球'),
((SELECT id FROM hobby_categories WHERE name = 'スポーツ'), 'バスケットボール'),
((SELECT id FROM hobby_categories WHERE name = 'スポーツ'), 'テニス');

INSERT IGNORE INTO hobby_items (category_id, name) VALUES
((SELECT id FROM hobby_categories WHERE name = '読書'), '小説'),
((SELECT id FROM hobby_categories WHERE name = '読書'), '漫画'),
((SELECT id FROM hobby_categories WHERE name = '読書'), 'ビジネス書');