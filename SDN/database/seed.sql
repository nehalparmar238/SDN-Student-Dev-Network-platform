USE sdn_db;

INSERT INTO `users` (`name`, `email`, `password_hash`, `college_course`, `role`) VALUES
('SDN Admin', 'admin@sdn.local',
'$2y$10$GhnGs.CLCFSTQIpr2okUE.f1C5WGwO/Gidp8erXuPk3dCEy6kjX7O',
'Administrator', 'admin');

INSERT INTO `project_category` (`category_name`, `description`) VALUES
('Web Development', 'Websites and web applications'),
('Mobile Apps', 'Android and iOS applications'),
('AI / ML', 'Artificial intelligence and machine learning'),
('IoT', 'Internet of Things and embedded systems'),
('Game Development', 'Games and interactive media'),
('Cyber Security', 'Security tools and research'),
('Data Science', 'Data analysis and visualization'),
('Other', 'Anything that does not fit above');