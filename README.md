# PeerFit (v1)

**Find people. Play sports. Stay active.**

PeerFit is a social sports platform for finding local players, connecting with others who share your interests, and building a community around sport. Manage your profile, explore interests and skill levels, chat with other players, and grow your network.

## Features

- User authentication with login, signup, and account management
- Profile management with sports interests and mastery levels
- User matching and friend requests with approval workflow
- Real-time chat messaging system with activity partners
- Activity and help request system
- Location-based features for finding local players
- Interest-based filtering and recommendation system

## How It Works

1. Sign up and create your profile with your sports interests
2. Set your mastery levels for different sports
3. Browse and match with other players who share your interests
4. Send friend requests to build your network
5. Chat with friends to coordinate activities and stay connected

## Tech Stack

- PHP 7+
- HTML5 / CSS3
- JavaScript (ES6)
- MySQL database
- jQuery

## Prerequisites

- XAMPP (or similar PHP development environment)
- PHP built-in server or Apache
- MySQL database

## Installation & Setup

1. **Start XAMPP:**
   - Launch XAMPP Control Panel
   - Start both Apache and MySQL modules
   - Ensure both show as running

2. **Download & Navigate:**
   - Download the Peerfit application files
   - Open terminal/command prompt
   - Navigate to the project directory: `cd path/to/Peerfit`

3. **Start Local Server:**
   - Run the PHP development server:
   ```bash
   php -S 127.0.0.1:8000
   ```
   - Or use Apache (ensure files are in htdocs folder)

4. **Access Peerfit:**
   - Open your browser and go to `http://127.0.0.1:8000/Startup.html`
   - Sign up or log in to get started

## Project Structure

- `Startup.html` — Landing/entry point
- `login.php`, `signup.php` — Authentication pages
- `home.php` — Main activity feed
- `profile.php` — User profile management
- `account.php` — Account settings
- `chat.php` — Messaging interface
- `match_users.php` — User discovery and matching
- `requests.php` — Manage friend requests
- `edit_interests.php` — Set sports interests and skill levels
- `Images/` — Static assets and user profile pictures

## Notes

- This is v1 of PeerFit. For the latest version, see the main branch documentation.
- Ensure MySQL is properly configured before running the application.
