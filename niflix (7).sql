-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2025 at 07:43 PM
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
(4, 3, 'adsfd', 'adsvfs', '2025-05-26 19:21:53', '2025-05-26 19:24:25'),
(6, 10, 'artikel user nick', 'apapun makanannya minumnya teh botol sostro', '2025-06-03 17:08:56', '2025-06-03 17:08:56'),
(7, 12, 'artikel user wilsan', 'apapun makanannya minumnya teh botol sostro', '2025-06-03 17:09:49', '2025-06-03 17:09:49'),
(8, 3, 'Pengalaman menonton film Shawshank Redemption', 'HEBATTT EUYYY HUHUUH', '2025-06-09 14:55:18', '2025-06-09 14:55:27'),
(10, 3, 'Coba Test', 'HOHOHOHOHOOHO', '2025-06-10 11:31:15', '2025-06-10 11:31:15'),
(13, 3, 'Coba Test1', 'kwkwkwwkwk', '2025-06-10 11:31:50', '2025-06-10 11:31:50');

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
(31, 3, 'yahh not bad', '2025-06-09 16:46:18', 11, 'series', NULL, 7),
(32, 15, 'nggak jelek jelek amat', '2025-06-10 06:25:37', 11, 'series', NULL, 7),
(33, 16, 'ngantukk', '2025-06-10 06:26:36', 11, 'series', NULL, 3),
(34, 3, 'heheeheh', '2025-06-10 09:14:06', 8, 'article', NULL, NULL),
(36, 3, 'bagus kok ini ahaaha', '2025-06-16 16:54:15', 7, 'film', NULL, 5),
(38, 17, 'ngantuk', '2025-06-16 17:14:17', 4, 'film', NULL, 4),
(39, 3, 'Jelek ah', '2025-06-16 17:17:33', 19, 'series', NULL, 4),
(40, 17, 'GOAT', '2025-06-16 17:18:21', 2, 'film', NULL, 9),
(43, 3, 'bagus ini', '2025-06-16 17:33:46', 16, 'film', NULL, 8),
(44, 3, 'jelek', '2025-06-16 17:39:31', 6, 'film', NULL, 4);

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_popular` smallint(6) DEFAULT 0 CHECK (`is_popular` in (0,1)),
  `creator_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `films`
--

INSERT INTO `films` (`id`, `title`, `description`, `image_url`, `release_year`, `created_at`, `updated_at`, `is_popular`, `creator_id`) VALUES
(1, 'The Shawshank Redemption', 'A banker convicted of uxoricide forms a friendship over a quarter century with a hardened convict, while maintaining his innocence and trying to remain hopeful through simple compassion.', 'https://upload.wikimedia.org/wikipedia/id/8/81/ShawshankRedemptionMoviePoster.jpg', 1994, '2025-06-15 08:35:15', '2025-06-15 08:35:15', 1, 3),
(2, 'The Godfather', 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.', 'https://m.media-amazon.com/images/M/MV5BNGEwYjgwOGQtYjg5ZS00Njc1LTk2ZGEtM2QwZWQ2NjdhZTE5XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 1972, '2025-06-15 09:02:58', '2025-06-16 12:40:02', 1, 3),
(3, 'The Dark Knight', 'When a menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman, James Gordon and Harvey Dent must work together to put an end to the madness.', 'https://upload.wikimedia.org/wikipedia/id/8/8a/Dark_Knight.jpg', 2008, '2025-06-15 09:28:20', '2025-06-15 16:17:21', 1, 3),
(4, '12 Angry Men', 'The jury in a New York City murder trial is frustrated by a single member whose skeptical caution forces them to more carefully consider the evidence before jumping to a hasty verdict.', 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/12_Angry_Men_%281957_film_poster%29.jpg/1200px-12_Angry_Men_%281957_film_poster%29.jpg', 1957, '2025-06-15 09:29:45', '2025-06-15 09:29:45', 0, 3),
(5, 'The Lord of the Rings', 'Gandalf and Aragorn lead the World of Men against Sauron\'s army to draw his gaze from Frodo and Sam as they approach Mount Doom with the One Ring.', 'https://upload.wikimedia.org/wikipedia/id/0/0d/EsdlaIII.jpg', 2003, '2025-06-15 09:31:25', '2025-06-15 15:29:06', 0, 3),
(6, 'Forrest Gump', 'The history of the United States from the 1950s to the \'70s unfolds from the perspective of an Alabama man with an IQ of 75, who yearns to be reunited with his childhood sweetheart.', 'https://upload.wikimedia.org/wikipedia/id/thumb/6/67/Forrest_Gump_poster.jpg/250px-Forrest_Gump_poster.jpg', 1994, '2025-06-15 14:46:10', '2025-06-15 16:04:13', 1, 3),
(7, 'Fight Club', 'An insomniac office worker and a devil-may-care soap maker form an underground fight club that evolves into much more.', 'https://m.media-amazon.com/images/M/MV5BOTgyOGQ1NDItNGU3Ny00MjU3LTg2YWEtNmEyYjBiMjI1Y2M5XkEyXkFqcGc@._V1_.jpg', 1999, '2025-06-15 14:48:56', '2025-06-15 16:19:01', 1, 3),
(16, 'Inception', 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O., but his tragic past may doom the project and his team to disaster.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRJyjBC4dx19LTH6CBmbDIpNCrelbYJSplrUA&s', 2010, '2025-06-15 16:17:00', '2025-06-15 16:17:00', 1, 3);

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
(27, 3, 5, 'series', '2025-06-09 11:00:26'),
(31, 3, 1, 'film', '2025-06-09 14:18:45'),
(39, 16, 11, 'comment', '2025-06-09 14:25:09'),
(42, 16, 19, 'comment', '2025-06-09 15:25:17'),
(47, 16, 11, 'series', '2025-06-10 06:28:53'),
(50, 3, 4, 'film', '2025-06-10 09:14:32'),
(59, 17, 41, 'comment', '2025-06-16 17:25:25'),
(60, 17, 42, 'comment', '2025-06-16 17:26:02'),
(75, 3, 43, 'comment', '2025-06-16 17:34:05');

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
(10, 7, 3, 'Film Untuk anak underground ini mah, film pemacu adrenalin dan juga film pengubah nyali seseorang.', '2025-06-16 12:38:30', '2025-06-16 12:38:30', 6);

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
(0, 1, 3, 'Ngantuk', '2025-06-16 12:36:40', '2025-06-16 12:36:40', 3);

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
  `is_popular` smallint(6) DEFAULT 0 CHECK (`is_popular` in (0,1)),
  `creator_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `series`
--

INSERT INTO `series` (`id`, `title`, `description`, `image_url`, `release_year`, `created_at`, `updated_at`, `is_popular`, `creator_id`) VALUES
(1, 'The Crown', 'Follows the reign of Queen Elizabeth II.', 'https://uploads.jovemnerd.com.br/wp-content/uploads/2022/10/the_crown_quinta_temporada__9zx2r8.jpg', 2016, '2025-06-06 02:38:00', '2025-06-10 06:44:10', 0, 3),
(2, 'Money Heist', 'A criminal mastermind who goes by \"The Professor\" has a plan to pull off the biggest heist in recorded history.', 'https://m.media-amazon.com/images/M/MV5BODI0ZTljYTMtODQ1NC00NmI0LTk1YWUtN2FlNDM1MDExMDlhXkEyXkFqcGdeQXVyMTM0NTUzNDIy._V1_FMjpg_UX1000_.jpg', 2017, '2025-06-06 02:38:00', '2025-06-16 10:05:50', 1, 3),
(3, 'Squid Game', 'Hundreds of cash-strapped players accept a strange invitation to compete in children\'s games.', 'https://m.media-amazon.com/images/M/MV5BNGFlOTBhMzYtYmU5OC00OGE2LWJkNzAtYzljOTk4ZjJlZjg2XkEyXkFqcGdeQXVyMTkxNjUyNQ@@._V1_FMjpg_UX1000_.jpg', 2021, '2025-06-06 02:38:00', '2025-06-10 06:44:19', 1, 3),
(4, 'Wednesday', 'Smart, sarcastic and a little dead inside, Wednesday Addams investigates a murder spree while making new friends — and foes — at Nevermore Academy.', 'https://media2.firstshowing.net/firstshowing/img14/Wednesdayseriespostermain59902.jpg', 2022, '2025-06-06 02:38:00', '2025-06-15 15:47:07', 0, 3),
(5, 'Breaking Bad', 'A high school chemistry teacher turned meth producer teams up with his former student to build a drug empire.', 'https://m.media-amazon.com/images/M/MV5BMzU5ZGYzNmQtMTdhYy00OGRiLTg0NmQtYjVjNzliZTg1ZGE4XkEyXkFqcGc@._V1_.jpg', 2008, '2025-06-06 02:38:00', '2025-06-10 06:44:27', 1, 3),
(6, 'From', 'The residents of a mysterious town struggle to survive against terrifying forces that come out at night.', 'https://images.justwatch.com/poster/305321213/s718/season-4.jpg', 2022, '2025-06-06 02:38:00', '2025-06-10 06:44:31', 1, 3),
(7, 'Mr.Robot', 'A brilliant but socially troubled hacker is recruited by a hacktivist group to take down a powerful corporation.', 'https://m.media-amazon.com/images/M/MV5BOTg4NTBiZDAtZTc0YS00NzZlLTg4Y2ItNGQ3M2ZlMDM5MWQzXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 2015, '2025-06-06 02:38:00', '2025-06-10 06:44:35', 1, 3),
(8, 'The Sopranos', 'The Sopranos revolves around the life of Tony Soprano, a powerful mob boss in New Jersey. While he manages the operations of his crime syndicate—dealing with rival gangs, betrayal, and maintaining control—he also struggles with deep personal issues, including anxiety attacks that lead him to seek therapy with Dr. Jennifer Melfi.\r\n\r\nThroughout the series, Tony juggles the demands of his brutal profession with his responsibilities as a father and husband. His relationships with his wife, Carmela, and his children, Meadow and A.J., add layers of tension and drama to the story. He also navigates conflicts within his own crew, including his volatile uncle Junior, his trusted but unpredictable right-hand man, Silvio Dante, and the rebellious Christopher Moltisanti.', 'https://m.media-amazon.com/images/M/MV5BMjRmMTNiMTQtMDg1ZS00MGM1LWE1MGUtYjEzMGFjNWUzOWRkXkEyXkFqcGc@._V1_.jpg', 1999, '2025-06-07 00:23:55', '2025-06-15 15:41:32', 0, 3),
(9, 'True Detective', 'True Detective presents gripping narratives that explore crime, morality, and the human psyche through different investigations. Each story follows detectives as they unravel deeply layered mysteries, often revealing the darkness within themselves and society.\r\n\r\nOne investigation uncovers eerie clues pointing to a cult, forcing two detectives to confront their own haunted pasts as the case stretches across decades. Another follows a tangled web of corruption in California, where multiple investigators search for truth in a world filled with deception. A later case involves missing children, with the investigation spanning generations as memory and time distort reality. In a frozen landscape, relentless officers chase chilling secrets after an entire team of researchers mysteriously disappears.', 'https://m.media-amazon.com/images/M/MV5BYjgwYzA1NWMtNDYyZi00ZGQyLWI5NTktMDYwZjE2OTIwZWEwXkEyXkFqcGc@._V1_.jpg', 2014, '2025-06-07 00:49:33', '2025-06-10 06:44:44', 0, 3),
(10, 'The Mist', 'A dense fog suddenly engulfs a small town, bringing with it an unexpected terror. As residents find themselves trapped in various locations, including a supermarket, they soon realize that something lurks within the mist. Fear and distrust grow as horrifying creatures emerge, and the situation worsens when people themselves reveal their darkest instincts.\r\n\r\nAmid the chaos, differing beliefs on survival spark conflict, making the ordeal even more intense. With hope fading, they must make difficult choices—stay in uncertainty or risk facing the unseen horrors. This story delves into psychological tension, human nature, and the idea that fear itself can be more dangerous than the actual threat.', 'https://m.media-amazon.com/images/M/MV5BMzE3MDk0ODkwM15BMl5BanBnXkFtZTgwMzA5NTk5MTI@._V1_.jpg', 2007, '2025-06-07 00:54:45', '2025-06-10 06:44:47', 1, 3),
(11, 'Peaky Blinders', 'A powerful and ambitious gang rises from the shadows of post-war Birmingham, forging a ruthless path through the criminal underworld. Led by a brilliant yet deeply tormented figure, the group navigates deadly rivalries, political entanglements, and personal betrayals, all while seeking dominance in a world governed by violence and shifting loyalties.\r\n\r\nAs tensions escalate, allegiances are tested, and old enemies resurface, forcing dangerous gambles that threaten everything they’ve built. Their relentless pursuit of power is matched only by their elegance, sharp minds, and unwavering sense of family.', 'https://m.media-amazon.com/images/M/MV5BOGM0NGY3ZmItOGE2ZC00OWIxLTk0N2EtZWY4Yzg3ZDlhNGI3XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 2013, '2025-06-07 00:59:34', '2025-06-10 06:44:51', 1, 3),
(13, '13 Reasons Why', 'A shocking revelation unfolds when a young woman leaves behind a series of recorded messages, detailing the painful experiences that led to her tragic decision. As those closest to her unravel the contents, they are forced to confront harsh truths about themselves, their actions, and the unseen consequences of everyday cruelty.\r\n\r\nWith each revelation, buried secrets emerge, relationships fracture, and the weight of guilt grows heavier. The journey through her perspective is not just a search for answers, but a devastating reflection on how small moments can shape lives in unimaginable ways.', 'https://m.media-amazon.com/images/M/MV5BNThjYzQ3ZjYtM2I0ZC00OGNjLTlmNGItYjE3ZWI5MWIyMWYyXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 2017, '2025-06-07 08:26:53', '2025-06-10 06:44:55', 0, 3),
(14, 'Narcos', 'In the chaotic world of drug trafficking, power is won through ruthless ambition and brutal force. As empires rise and fall, key figures maneuver through dangerous alliances, betrayals, and relentless pursuits by law enforcement. The battle between those who enforce the law and those who defy it blurs into a complex game where survival depends on strategy, deception, and an unbreakable will.\r\n\r\nWith fortunes built on violence and influence, every decision carries high stakes—an empire can crumble with one wrong move. The pursuit of dominance stretches beyond borders, entangling politicians, military forces, and revolutionaries in a war that seems endless.', 'https://m.media-amazon.com/images/M/MV5BNzQwOTcwMzIwN15BMl5BanBnXkFtZTgwMjYxMTA0NjE@._V1_FMjpg_UX1000_.jpg', 2015, '2025-06-07 08:27:49', '2025-06-10 06:44:59', 0, 3),
(15, 'Prison Break', 'A desperate plan takes shape when a brilliant structural engineer devises an elaborate escape to free his wrongfully accused brother from a maximum-security prison. With every detail carefully mapped out, he infiltrates the prison himself, embedding clues and hidden tools within intricate tattoos covering his body.\r\n\r\nInside the facility, alliances are formed, rivalries intensify, and unforeseen obstacles threaten to derail the plan. As the escape unfolds, the stakes grow higher, dragging everyone involved into a web of conspiracy, deception, and relentless pursuit.', 'https://resizing.flixster.com/-XZAfHZM39UwaGJIFWKAE8fS0ak=/v3/t/assets/p185128_b_v8_ag.jpg', 2005, '2025-06-07 08:30:08', '2025-06-10 06:45:03', 1, 3),
(16, 'Dahmer – Monster', 'A chilling portrayal of one of history’s most notorious criminals, this harrowing account delves into the deeply disturbing life of a man whose actions shocked the world. Through the perspectives of his victims, law enforcement, and the societal failures that allowed his crimes to continue, the story exposes the unsettling reality behind the monster.\r\n\r\nAs authorities struggle to connect the pieces, the narrative reveals missed opportunities, overlooked warnings, and the consequences of systemic neglect. It’s a haunting exploration of power, manipulation, and the lasting scars left on those affected', 'https://upload.wikimedia.org/wikipedia/en/2/28/Dahmer_netflix_series.jpg', 2022, '2025-06-07 08:34:40', '2025-06-10 06:45:06', 0, 3),
(17, 'The Boys', 'In a world where superheroes are idolized and commercialized, a dark reality lurks beneath their glamorous facade. Behind the costumes and carefully crafted public personas, these so-called heroes abuse their power, engaging in corruption, violence, and manipulation—all while being protected by a powerful corporation that ensures their secrets remain hidden.\r\n\r\nAmid the deception, a group of vigilantes emerges, determined to expose the truth and bring justice to those who have suffered at the hands of these untouchable figures. Armed with nothing but grit, strategy, and their own personal vendettas, they take on the seemingly invincible, knowing the fight could cost them everything', 'https://imusic.b-cdn.net/images/item/original/533/5035822053533.jpg?the-boys-season-1-2020-boys-the-2019-season-1-dvd&amp;class=scaled&amp;v=1646590619', 2019, '2025-06-07 08:38:41', '2025-06-15 15:47:30', 0, 3),
(18, 'Better Call Saul', 'A cunning and ambitious lawyer navigates the murky world of legal loopholes, shady deals, and moral compromises, determined to carve out a name for himself. Starting with small-time cases and unconventional tactics, he slowly transforms into a force to be reckoned with—though not always on the right side of the law.\r\n\r\nAs alliances shift, personal struggles intensify, and dangerous opportunities arise, his journey becomes one of survival, reinvention, and inevitable consequences. What begins as a pursuit of legitimacy spirals into a high-stakes game where deception and brilliance go hand in hand.', 'https://resizing.flixster.com/-XZAfHZM39UwaGJIFWKAE8fS0ak=/v3/t/assets/p10492751_b_v13_al.jpg', 2015, '2025-06-07 10:07:38', '2025-06-10 06:45:13', 1, 3),
(19, 'Chernobyl', 'A gripping portrayal of a catastrophic event that reshaped history, this narrative delves into the harrowing consequences of human error, secrecy, and bravery. The tension builds as individuals are forced to confront the unimaginable, battling against unseen dangers and their own fears. As the tragedy escalates, acts of heroism emerge in the midst of devastation, shedding light on the resilience of those who fought to contain the disaster. Their sacrifices stand as a testament to courage in the face of overwhelming adversity.\r\n\r\nA chilling reminder of the cost of misinformation and the power of truth, this tale leaves a lasting impact on all who witness its unfolding. Through striking moments of despair and defiance, it explores the fragile balance between science, politics, and humanity. Every detail serves to highlight the devastating effects of negligence and the unwavering determination to seek justice. Ultimately, it provokes reflection on the lessons learned from one of history’s darkest chapters.', 'https://resizing.flixster.com/wdwswQucPh6IuTl8L2ZCCsOd8yQ=/fit-in/705x460/v2/https://resizing.flixster.com/-XZAfHZM39UwaGJIFWKAE8fS0ak=/v3/t/assets/p16686729_b_v13_ad.jpg', 2019, '2025-06-10 06:50:23', '2025-06-10 07:11:11', 0, 17);

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
(3, 'admin', 'admin@niflix.com', '$2y$10$QFx.SAgJZD6EX.Mi0Ds8iOWZsbMd1j/39dnjLs8sLSP9TU7BqiXfm', 'Admin Niflix', 'default.png', 1),
(10, 'user1', 'user1@gmail.com', '$2y$10$3iM3ilCYRkbbsJxbRl2C5eeX25ZZPXw3d262AP48HtK7E1V2XEoRW', 'user1', 'default.png', 0),
(12, 'user2', 'user2@gmail.com', '$2y$10$tM556JF65OyO.iD4HZSL7OGKmW8jRU7ncU6ZJYxETGp9kI6Dq2Xhe', 'user2', 'default.png', 0),
(14, 'admin1', 'admin1@gmail.com', '$2y$10$xdOZ3NGjeEfitGVhHkuw.uM7KfC57.wRe9Bax2KbnvMzEwP8p/gQq', 'admin1', 'default.png', 0),
(15, 'dzaki1', 'sjsj@gsh.com', '$2y$10$P1een/6sgieDJa6uf6unDuZlZqZ5omtR9cuVZDERZuLuzyRNNUF36', 'dzaki1', 'default.png', 0),
(16, 'dzaki2', 'hshsh@hdhdh.co', '$2y$10$dgtm.JjiEfDfFUZ.8vgFMui/OzEh7kUAWSHcMWkiTD5bgCSG4xQHS', 'dzaki2', 'default.png', 0),
(17, 'admin2', 'admin2@niflix.com', '$2y$10$dTkbO.thxiRbdPCDgxo9..UqdhFszV.ZqvA56n9K48yGRPL21j/TW', 'admin2', 'default.png', 1),
(18, 'isan', 'isan@gmail.com', '$2y$10$ZBpAb9LZPfGgOxvc5AJpPOBg3t4Gxz/Xvu9nfvh6CI3yWrZ5XD0aS', 'isan', 'default.png', 0),
(19, 'admin4', 'hsh@dhd.co', '$2y$10$ulCIiPrfHNlhZ382juAnLekEWQ5agjNL69a1NcUEBRe2y2wdlSlim', 'admin4', 'default.png', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_articles_title` (`title`),
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
-- Indexes for table `films`
--
ALTER TABLE `films`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`,`creator_id`);

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
  ADD UNIQUE KEY `title` (`title`),
  ADD KEY `fk_series_creator` (`creator_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `comments_rating`
--
ALTER TABLE `comments_rating`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `films`
--
ALTER TABLE `films`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `review_films`
--
ALTER TABLE `review_films`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `series`
--
ALTER TABLE `series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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

--
-- Constraints for table `series`
--
ALTER TABLE `series`
  ADD CONSTRAINT `fk_series_creator` FOREIGN KEY (`creator_id`) REFERENCES `user` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
