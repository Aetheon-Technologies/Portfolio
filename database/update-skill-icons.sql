-- ============================================================
-- Add skill icons using Devicons CDN (no download required)
-- Run in phpMyAdmin: portfolio database → SQL tab → paste → Go
-- ============================================================

USE portfolio;

-- Frontend
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-original.svg'           WHERE name = 'HTML / CSS';
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-original.svg' WHERE name = 'JavaScript';
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/react/react-original.svg'           WHERE name = 'React';
UPDATE skills SET icon_url = NULL                                                                                              WHERE name = 'Tailwind CSS';

-- Backend
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-original.svg'              WHERE name = 'PHP';
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/nodejs/nodejs-original.svg'        WHERE name = 'Node.js';
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/python/python-original.svg'        WHERE name = 'Python';
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/fastapi/fastapi-original.svg'      WHERE name = 'REST APIs';

-- Database & DevOps
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/mysql/mysql-original.svg'          WHERE name = 'MySQL';
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/github/github-original.svg'          WHERE name = 'Git / GitHub';
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/linux/linux-original.svg'          WHERE name = 'Linux';
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/docker/docker-original.svg'        WHERE name = 'Docker';

-- Robotics & Hardware
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/arduino/arduino-original.svg'      WHERE name = 'Arduino';
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/raspberrypi/raspberrypi-original.svg' WHERE name = 'Raspberry Pi';
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/cplusplus/cplusplus-original.svg'  WHERE name = 'C / C++';
UPDATE skills SET icon_url = 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/ros/ros-original.svg'              WHERE name = 'ROS';
