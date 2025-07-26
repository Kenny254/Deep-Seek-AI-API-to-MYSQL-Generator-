# 📰 AI News Auto-Poster with DeepSeek API (PHP)

## 👋 Hello and Welcome!

Hi there! 👋  
I built this project with you in mind — to help you automate news generation and publishing using AI. If you're running a blog, news site, or just experimenting with automation, this PHP script connects to the DeepSeek API and auto-generates fresh, Kenyan-focused news articles in categories like Technology, Politics, Business, Medicine, Education, and Entertainment.

It not only writes the articles for you but also inserts them directly into your MySQL database — while avoiding duplicates, assigning slugs, setting metadata, and logging each run. It's lightweight, cron-job friendly, and fully customizable. Let’s get you up and running in minutes!

---

## ⚙️ Configuration

Before using the script, open the PHP file and update the following placeholders:

- `DB_USERNAME` – your MySQL database username  
- `DB_PASSWORD` – your database password  
- `DB_NAME` – the name of your database  
- `YOUR_DEEPSEEK_API_KEY` – your DeepSeek API key  

Make sure you have the following table set up in your database:

```sql
CREATE TABLE `gonews` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `category` VARCHAR(32) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `author_id` INT(11) DEFAULT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `is_top` TINYINT(1) DEFAULT 0,
  `status` ENUM('draft','published','archived') DEFAULT 'draft',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
