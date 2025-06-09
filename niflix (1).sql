-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 01:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `niflix`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `user_id`, `title`, `content`, `created_at`, `updated_at`) VALUES
(3, 3, 'adsfd', 'sfd', '2025-05-26 19:21:10', '2025-05-26 19:21:10'),
(4, 3, 'adsfd', 'adsvfs', '2025-05-26 19:21:53', '2025-05-26 19:24:25'),
(6, 10, 'artikel user nick', 'apapun makanannya minumnya teh botol sostro', '2025-06-03 17:08:56', '2025-06-03 17:08:56'),
(7, 12, 'artikel user wilsan', 'apapun makanannya minumnya teh botol sostro', '2025-06-03 17:09:49', '2025-06-03 17:09:49');

-- --------------------------------------------------------

--
-- Table structure for table `comments_rating`
--

CREATE TABLE `comments_rating` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `item_id` int(11) DEFAULT NULL,
  `item_type` enum('film','series','article') NOT NULL DEFAULT 'article',
  `parent_comment_id` int(11) DEFAULT NULL,
  `rating_value` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments_rating`
--

INSERT INTO `comments_rating` (`id`, `user_id`, `comment_text`, `created_at`, `item_id`, `item_type`, `parent_comment_id`, `rating_value`) VALUES
(1, 3, 'csvc', '2025-05-26 19:24:37', 4, 'series', NULL, NULL),
(5, 10, 'apapun makanannya minumnya teh botol sostro', '2025-06-03 17:09:07', 4, 'series', NULL, NULL),
(6, 12, 'apapun makanannya minumnya teh botol sostro', '2025-06-03 17:09:53', 4, 'series', NULL, NULL),
(7, 12, 'apapun makanannya minumnya teh botol sostro', '2025-06-03 17:10:00', 4, 'series', NULL, NULL),
(10, 3, 'bagussss', '2025-06-09 09:45:03', 4, 'series', NULL, NULL),
(11, 15, 'WOWW keren', '2025-06-09 10:06:49', 4, 'film', NULL, NULL),
(12, 15, 'bagus kok', '2025-06-09 10:24:01', 4, 'film', NULL, 9),
(13, 3, 'bagi aku jelek sih', '2025-06-09 10:24:37', 4, 'film', NULL, 4),
(14, 3, '', '2025-06-09 10:55:17', 2, 'series', NULL, 5),
(15, 3, 'biasa ajaa', '2025-06-09 10:57:34', 2, 'series', NULL, NULL),
(16, 3, 'bagus', '2025-06-09 11:01:56', 1, 'series', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `films`
--

CREATE TABLE `films` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `release_year` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `films`
--

INSERT INTO `films` (`id`, `title`, `description`, `image_url`, `release_year`, `created_at`, `updated_at`) VALUES
(1, 'Inception', 'A skilled thief who specializes in dream extraction is tasked with planting an idea into someone\'s mind.', 'https://tse1.mm.bing.net/th?id=OIP.asgzvFKYUGe5x_vM49WGxwHaEK&pid=Api&P=0&h=180', 2010, '2025-05-26 23:38:20', '2025-05-26 23:38:20'),
(2, 'Interstellar', 'A space expedition seeks a new habitable planet for humanity as Earth faces extinction.', 'https://image.tmdb.org/t/p/original/djS3XxneEFjCM6VlCiuuN8QavE6.jpg', 2014, '2025-05-26 23:38:20', '2025-05-26 23:38:20'),
(3, 'The Dark Knight', 'Batman battles the Joker, a mysterious criminal who spreads chaos in Gotham.', 'https://tse1.mm.bing.net/th?id=OIP.Z-abpaw6SQyGf6THsACc4wHaEK&pid=Api&P=0&h=180', 2008, '2025-05-26 23:38:20', '2025-05-26 23:38:20'),
(4, 'Avatar', 'A paraplegic Marine is sent to the world of Pandora and becomes involved in the Na\'vi struggle.', 'https://tse1.mm.bing.net/th?id=OIP.Lf9cnLcjGK_xQaNBCxk6HQHaEK&pid=Api&P=0&h=180', 2009, '2025-05-26 23:38:20', '2025-05-26 23:38:20'),
(5, 'The Matrix', 'A hacker discovers the world he knows is a simulation and joins a rebellion.', 'https://tse4.mm.bing.net/th?id=OIP.t6sG8tVVbTigh1q1kJNE6wHaEK&pid=Api&P=0&h=180', 1999, '2025-05-26 23:38:20', '2025-05-26 23:38:20');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_type` enum('film','series','comment') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `item_id`, `item_type`, `created_at`) VALUES
(1, 3, 5, 'comment', '2025-06-09 09:45:29'),
(4, 3, 1, 'comment', '2025-06-09 09:45:38'),
(6, 15, 5, 'comment', '2025-06-09 09:46:06'),
(15, 15, 11, 'comment', '2025-06-09 10:07:23'),
(17, 3, 6, 'comment', '2025-06-09 10:37:12'),
(18, 3, 7, 'comment', '2025-06-09 10:37:19'),
(22, 3, 2, 'series', '2025-06-09 10:59:07'),
(24, 3, 1, 'series', '2025-06-09 10:59:50'),
(27, 3, 5, 'series', '2025-06-09 11:00:26');

-- --------------------------------------------------------

--
-- Table structure for table `review_films`
--

CREATE TABLE `review_films` (
  `id` int(11) NOT NULL,
  `film_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rating` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review_films`
--

INSERT INTO `review_films` (`id`, `film_id`, `user_id`, `review_text`, `created_at`, `updated_at`, `rating`) VALUES
(1, 1, 1, 'Filmnya sangat mind-blowing, terutama konsep mimpi dalam mimpi.', '2025-05-31 20:00:00', '2025-06-08 05:55:23', 0),
(2, 2, 2, 'Visual dan alur waktu di Interstellar bikin merinding. Top banget.', '2025-05-31 21:00:00', '2025-06-08 05:55:23', 0),
(3, 3, 3, 'The Joker benar-benar karakter villain terbaik yang pernah ada.', '2025-05-31 22:00:00', '2025-06-08 05:55:23', 0),
(4, 4, 1, 'Dunia Pandora itu sangat indah. Teknologi CGI-nya luar biasa.', '2025-06-01 19:30:00', '2025-06-08 05:55:23', 0),
(5, 5, 2, 'Matrix bikin kita mempertanyakan realitas. Cerita yang sangat keren.', '2025-06-01 20:15:00', '2025-06-08 05:55:23', 0),
(6, 1, 3, 'Nonton Inception dua kali baru ngerti semua plot-nya.', '2025-06-01 21:45:00', '2025-06-08 05:55:23', 0),
(7, 3, 2, 'Batman di film ini sangat keren. Tapi Joker yang paling nyuri perhatian.', '2025-06-02 18:25:00', '2025-06-08 05:55:23', 0),
(8, 4, 15, 'avatar udh pasti bagus', '2025-06-08 05:34:04', '2025-06-08 05:55:23', 0),
(9, 4, 3, 'Bagus kok avatar', '2025-06-08 05:41:10', '2025-06-08 05:55:23', 0);

-- --------------------------------------------------------

--
-- Table structure for table `review_series`
--

CREATE TABLE `review_series` (
  `id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rating` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review_series`
--

INSERT INTO `review_series` (`id`, `series_id`, `user_id`, `review_text`, `created_at`, `updated_at`, `rating`) VALUES
(0, 1, 3, 'Ngantukkk', '2025-06-09 07:53:20', '2025-06-09 07:53:20', 0),
(5, 1, 1, 'The Crown provides a fascinating look into the reign of Queen Elizabeth II with excellent acting and production.', '2025-06-03 12:35:53', '2025-06-03 12:35:53', 0),
(6, 2, 2, 'Money Heist is thrilling and full of unexpected twists. The Professor\'s plan is genius and keeps you hooked.', '2025-06-03 12:35:53', '2025-06-03 12:35:53', 0),
(7, 3, 3, 'Squid Game is intense and thought-provoking, combining children\'s games with high stakes drama effectively.', '2025-06-03 12:35:53', '2025-06-03 12:35:53', 0),
(8, 4, 4, 'Wednesday is a fresh and darkly humorous take on the Addams Family, with a compelling mystery at its core.', '2025-06-03 12:35:53', '2025-06-02 17:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `series`
--

CREATE TABLE `series` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `release_year` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_popular` smallint(6) DEFAULT 0 CHECK (`is_popular` in (0,1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `series`
--

INSERT INTO `series` (`id`, `title`, `description`, `image_url`, `release_year`, `created_at`, `updated_at`, `is_popular`) VALUES
(1, 'The Crown', 'Follows the reign of Queen Elizabeth II.', 'https://uploads.jovemnerd.com.br/wp-content/uploads/2022/10/the_crown_quinta_temporada__9zx2r8.jpg', 2016, '2025-06-06 02:38:00', '2025-06-06 02:40:28', 0),
(2, 'Money Heist', 'A criminal mastermind who goes by \"The Professor\" has a plan to pull off the biggest heist in recorded history.', 'https://m.media-amazon.com/images/M/MV5BODI0ZTljYTMtODQ1NC00NmI0LTk1YWUtN2FlNDM1MDExMDlhXkEyXkFqcGdeQXVyMTM0NTUzNDIy._V1_FMjpg_UX1000_.jpg', 2017, '2025-06-06 02:38:00', '2025-06-07 00:16:17', 1),
(3, 'Squid Game', 'Hundreds of cash-strapped players accept a strange invitation to compete in children\'s games.', 'https://m.media-amazon.com/images/M/MV5BNGFlOTBhMzYtYmU5OC00OGE2LWJkNzAtYzljOTk4ZjJlZjg2XkEyXkFqcGdeQXVyMTkxNjUyNQ@@._V1_FMjpg_UX1000_.jpg', 2021, '2025-06-06 02:38:00', '2025-06-06 07:36:17', 1),
(4, 'Wednesday', 'Smart, sarcastic and a little dead inside, Wednesday Addams investigates a murder spree while making new friends — and foes — at Nevermore Academy.', 'https://media2.firstshowing.net/firstshowing/img14/Wednesdayseriespostermain59902.jpg', 2022, '2025-06-06 02:38:00', '2025-06-07 00:16:55', 1),
(5, 'Breaking Bad', 'A high school chemistry teacher turned meth producer teams up with his former student to build a drug empire.', 'https://m.media-amazon.com/images/M/MV5BMzU5ZGYzNmQtMTdhYy00OGRiLTg0NmQtYjVjNzliZTg1ZGE4XkEyXkFqcGc@._V1_.jpg', 2008, '2025-06-06 02:38:00', '2025-06-06 07:36:17', 1),
(6, 'From', 'The residents of a mysterious town struggle to survive against terrifying forces that come out at night.', 'https://images.justwatch.com/poster/305321213/s718/season-4.jpg', 2022, '2025-06-06 02:38:00', '2025-06-06 07:36:17', 1),
(7, 'Mr.Robot', 'A brilliant but socially troubled hacker is recruited by a hacktivist group to take down a powerful corporation.', 'https://m.media-amazon.com/images/M/MV5BOTg4NTBiZDAtZTc0YS00NzZlLTg4Y2ItNGQ3M2ZlMDM5MWQzXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 2015, '2025-06-06 02:38:00', '2025-06-06 07:36:17', 1),
(8, 'The Sopranos', 'The Sopranos revolves around the life of Tony Soprano, a powerful mob boss in New Jersey. While he manages the operations of his crime syndicate—dealing with rival gangs, betrayal, and maintaining control—he also struggles with deep personal issues, including anxiety attacks that lead him to seek therapy with Dr. Jennifer Melfi.\r\n\r\nThroughout the series, Tony juggles the demands of his brutal profession with his responsibilities as a father and husband. His relationships with his wife, Carmela, and his children, Meadow and A.J., add layers of tension and drama to the story. He also navigates conflicts within his own crew, including his volatile uncle Junior, his trusted but unpredictable right-hand man, Silvio Dante, and the rebellious Christopher Moltisanti.', 'https://m.media-amazon.com/images/M/MV5BMjRmMTNiMTQtMDg1ZS00MGM1LWE1MGUtYjEzMGFjNWUzOWRkXkEyXkFqcGc@._V1_.jpg', 1999, '2025-06-07 00:23:55', '2025-06-07 00:27:40', 1),
(9, 'True Detective', 'True Detective presents gripping narratives that explore crime, morality, and the human psyche through different investigations. Each story follows detectives as they unravel deeply layered mysteries, often revealing the darkness within themselves and society.\r\n\r\nOne investigation uncovers eerie clues pointing to a cult, forcing two detectives to confront their own haunted pasts as the case stretches across decades. Another follows a tangled web of corruption in California, where multiple investigators search for truth in a world filled with deception. A later case involves missing children, with the investigation spanning generations as memory and time distort reality. In a frozen landscape, relentless officers chase chilling secrets after an entire team of researchers mysteriously disappears.', 'https://m.media-amazon.com/images/M/MV5BYjgwYzA1NWMtNDYyZi00ZGQyLWI5NTktMDYwZjE2OTIwZWEwXkEyXkFqcGc@._V1_.jpg', 2014, '2025-06-07 00:49:33', '2025-06-07 00:51:11', 0),
(10, 'The Mist', 'A dense fog suddenly engulfs a small town, bringing with it an unexpected terror. As residents find themselves trapped in various locations, including a supermarket, they soon realize that something lurks within the mist. Fear and distrust grow as horrifying creatures emerge, and the situation worsens when people themselves reveal their darkest instincts.\r\n\r\nAmid the chaos, differing beliefs on survival spark conflict, making the ordeal even more intense. With hope fading, they must make difficult choices—stay in uncertainty or risk facing the unseen horrors. This story delves into psychological tension, human nature, and the idea that fear itself can be more dangerous than the actual threat.', 'https://m.media-amazon.com/images/M/MV5BMzE3MDk0ODkwM15BMl5BanBnXkFtZTgwMzA5NTk5MTI@._V1_.jpg', 2007, '2025-06-07 00:54:45', '2025-06-07 01:01:57', 1),
(11, 'Peaky Blinders', 'A powerful and ambitious gang rises from the shadows of post-war Birmingham, forging a ruthless path through the criminal underworld. Led by a brilliant yet deeply tormented figure, the group navigates deadly rivalries, political entanglements, and personal betrayals, all while seeking dominance in a world governed by violence and shifting loyalties.\r\n\r\nAs tensions escalate, allegiances are tested, and old enemies resurface, forcing dangerous gambles that threaten everything they’ve built. Their relentless pursuit of power is matched only by their elegance, sharp minds, and unwavering sense of family.', 'https://m.media-amazon.com/images/M/MV5BOGM0NGY3ZmItOGE2ZC00OWIxLTk0N2EtZWY4Yzg3ZDlhNGI3XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 2013, '2025-06-07 00:59:34', '2025-06-07 00:59:34', 1),
(13, '13 Reasons Why', 'A shocking revelation unfolds when a young woman leaves behind a series of recorded messages, detailing the painful experiences that led to her tragic decision. As those closest to her unravel the contents, they are forced to confront harsh truths about themselves, their actions, and the unseen consequences of everyday cruelty.\r\n\r\nWith each revelation, buried secrets emerge, relationships fracture, and the weight of guilt grows heavier. The journey through her perspective is not just a search for answers, but a devastating reflection on how small moments can shape lives in unimaginable ways.', 'https://m.media-amazon.com/images/M/MV5BNThjYzQ3ZjYtM2I0ZC00OGNjLTlmNGItYjE3ZWI5MWIyMWYyXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 2017, '2025-06-07 08:26:53', '2025-06-07 08:26:53', 0),
(14, 'Narcos', 'In the chaotic world of drug trafficking, power is won through ruthless ambition and brutal force. As empires rise and fall, key figures maneuver through dangerous alliances, betrayals, and relentless pursuits by law enforcement. The battle between those who enforce the law and those who defy it blurs into a complex game where survival depends on strategy, deception, and an unbreakable will.\r\n\r\nWith fortunes built on violence and influence, every decision carries high stakes—an empire can crumble with one wrong move. The pursuit of dominance stretches beyond borders, entangling politicians, military forces, and revolutionaries in a war that seems endless.', 'https://m.media-amazon.com/images/M/MV5BNzQwOTcwMzIwN15BMl5BanBnXkFtZTgwMjYxMTA0NjE@._V1_FMjpg_UX1000_.jpg', 2015, '2025-06-07 08:27:49', '2025-06-07 08:27:49', 0),
(15, 'Prison Break', 'A desperate plan takes shape when a brilliant structural engineer devises an elaborate escape to free his wrongfully accused brother from a maximum-security prison. With every detail carefully mapped out, he infiltrates the prison himself, embedding clues and hidden tools within intricate tattoos covering his body.\r\n\r\nInside the facility, alliances are formed, rivalries intensify, and unforeseen obstacles threaten to derail the plan. As the escape unfolds, the stakes grow higher, dragging everyone involved into a web of conspiracy, deception, and relentless pursuit.', 'https://resizing.flixster.com/-XZAfHZM39UwaGJIFWKAE8fS0ak=/v3/t/assets/p185128_b_v8_ag.jpg', 2005, '2025-06-07 08:30:08', '2025-06-07 08:30:54', 1),
(16, 'Dahmer – Monster', 'A chilling portrayal of one of history’s most notorious criminals, this harrowing account delves into the deeply disturbing life of a man whose actions shocked the world. Through the perspectives of his victims, law enforcement, and the societal failures that allowed his crimes to continue, the story exposes the unsettling reality behind the monster.\r\n\r\nAs authorities struggle to connect the pieces, the narrative reveals missed opportunities, overlooked warnings, and the consequences of systemic neglect. It’s a haunting exploration of power, manipulation, and the lasting scars left on those affected', 'https://upload.wikimedia.org/wikipedia/en/2/28/Dahmer_netflix_series.jpg', 2022, '2025-06-07 08:34:40', '2025-06-07 19:28:42', 0),
(17, 'The Boys', 'In a world where superheroes are idolized and commercialized, a dark reality lurks beneath their glamorous facade. Behind the costumes and carefully crafted public personas, these so-called heroes abuse their power, engaging in corruption, violence, and manipulation—all while being protected by a powerful corporation that ensures their secrets remain hidden.\r\n\r\nAmid the deception, a group of vigilantes emerges, determined to expose the truth and bring justice to those who have suffered at the hands of these untouchable figures. Armed with nothing but grit, strategy, and their own personal vendettas, they take on the seemingly invincible, knowing the fight could cost them everything', 'https://imusic.b-cdn.net/images/item/original/533/5035822053533.jpg?the-boys-season-1-2020-boys-the-2019-season-1-dvd&class=scaled&v=1646590619', 2019, '2025-06-07 08:38:41', '2025-06-07 08:38:41', 1),
(18, 'Better Call Saul', 'A cunning and ambitious lawyer navigates the murky world of legal loopholes, shady deals, and moral compromises, determined to carve out a name for himself. Starting with small-time cases and unconventional tactics, he slowly transforms into a force to be reckoned with—though not always on the right side of the law.\r\n\r\nAs alliances shift, personal struggles intensify, and dangerous opportunities arise, his journey becomes one of survival, reinvention, and inevitable consequences. What begins as a pursuit of legitimacy spirals into a high-stakes game where deception and brilliance go hand in hand.', 'https://resizing.flixster.com/-XZAfHZM39UwaGJIFWKAE8fS0ak=/v3/t/assets/p10492751_b_v13_al.jpg', 2015, '2025-06-07 10:07:38', '2025-06-07 10:07:38', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(10) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `foto_pengguna` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `nama_lengkap`, `foto_pengguna`, `is_admin`) VALUES
(3, 'admin', 'admin@niflix.com', '$2y$10$QFx.SAgJZD6EX.Mi0Ds8iOWZsbMd1j/39dnjLs8sLSP9TU7BqiXfm', 'Admin Niflix', 'user_3_1748283135.jpg', 1),
(10, 'user1', 'user1@gmail.com', '$2y$10$3iM3ilCYRkbbsJxbRl2C5eeX25ZZPXw3d262AP48HtK7E1V2XEoRW', 'user1', 'default.png', 0),
(12, 'user2', 'user2@gmail.com', '$2y$10$tM556JF65OyO.iD4HZSL7OGKmW8jRU7ncU6ZJYxETGp9kI6Dq2Xhe', 'user2', 'default.png', 0),
(14, 'admin1', 'admin1@gmail.com', '$2y$10$xdOZ3NGjeEfitGVhHkuw.uM7KfC57.wRe9Bax2KbnvMzEwP8p/gQq', 'admin1', 'default.png', 0),
(15, 'dzaki1', 'sjsj@gsh.com', '$2y$10$P1een/6sgieDJa6uf6unDuZlZqZ5omtR9cuVZDERZuLuzyRNNUF36', 'dzaki1', 'default.png', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comments_rating`
--
ALTER TABLE `comments_rating`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_comments_rating_item` (`item_id`,`item_type`),
  ADD KEY `idx_comments_rating_parent` (`parent_comment_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`item_id`,`item_type`);

--
-- Indexes for table `review_films`
--
ALTER TABLE `review_films`
  ADD PRIMARY KEY (`id`),
  ADD KEY `film_id` (`film_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `review_series`
--
ALTER TABLE `review_series`
  ADD PRIMARY KEY (`id`),
  ADD KEY `series_id` (`series_id`),
  ADD KEY `user_id ` (`user_id`);

--
-- Indexes for table `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `comments_rating`
--
ALTER TABLE `comments_rating`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `review_films`
--
ALTER TABLE `review_films`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `series`
--
ALTER TABLE `series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments_rating`
--
ALTER TABLE `comments_rating`
  ADD CONSTRAINT `comments_rating_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_parent_comment` FOREIGN KEY (`parent_comment_id`) REFERENCES `comments_rating` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
