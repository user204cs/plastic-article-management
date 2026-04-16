# Simec App

A fullstack decoupled application featuring a modern Vue.js frontend and a robust PHP backend, interacting with an SQL Server database.

## 🚀 Technologies Used

### Frontend
* **Vue 3** (Composition API)
* **Vite** (Build tool & development server)
* **Vue Router** (Client-side routing)
* **CSS/Styling** (Custom components, modals, and responsive design)

### Backend
* **PHP** (Object-Oriented Architecture: Models, Controllers, Config)
* **SQL Server** (Database, connected via ODBC)
* **RESTful API Architecture** (Separated endpoints for Demandes, Products, Familles, Auth, etc.)

## 📁 Project Structure

```text
simec-app/
├── backend/                # PHP Backend implementation
│   ├── api/                # REST API endpoints (auth, demandes, products, etc.)
│   ├── classes/            # PHP Object Models
│   ├── config/             # Database configuration
│   └── database.sql        # Database schema and setup
├── src/                    # Vue.js Frontend implementation
│   ├── components/         # Reusable Vue components (Cards, Modals)
│   ├── router/             # Vue Router configuration
│   ├── services/           # API interaction layer (api.js)
│   └── views/              # Main application pages
├── .env.example            # Environment variables example
└── index.html              # Vue application entry point
```

## 🛠️ Getting Started

### Prerequisites
* **Node.js** and **npm** installed.
* A local web server stack (like **XAMPP, WAMP, or Laragon**) for running PHP.
* **SQL Server** (or access to an existing instance).

### 1. Backend Setup (PHP & Database)

1. Move or clone this project into your web server's document root (e.g., `htdocs` or `www`).
2. Start your Apache server (ensure it runs on port `8080` or update the frontend `.env` to match your port).
3. Set up the Database:
   * Copy `backend/config/database.example.php` to `backend/config/database.php`.
   * Open `database.php` and fill in your actual database credentials (username and password). *Note: `database.php` is ignored by Git for security.*
   * Import the `backend/database.sql` schema into your SQL Server if necessary.

### 2. Frontend Setup (Vue.js)

1. Open a terminal in the root of the project.
2. Install frontend dependencies:
   ```bash
   npm install
   ```
3. Configure Environment Variables:
   * Create a `.env` file in the root directory (you can copy from `.env.example` if available).
   * Define your API base URL:
     ```env
     VITE_API_BASE_URL=http://localhost:8080/simec-app/backend/api
     ```
4. Start the Vite development server:
   ```bash
   npm run dev
   ```
5. Open the displayed local URL (usually `http://localhost:5173/`) in your browser.

## 🔒 Security Notes
* Database credentials and `.env` files are deliberately excluded from version control via `.gitignore`.
* Cross-Origin Resource Sharing (CORS) is configured on the frontend HTTP requests to connect securely to the decoupled backend.
