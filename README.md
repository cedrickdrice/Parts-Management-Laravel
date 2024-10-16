# Project Name

## Description
This project is a parts management system designed to manage team parts, including uploading parts from CSV files, associating parts with teams, and retrieving parts information.

## Tech Stack
- **Backend:** Laravel
- **Frontend:** Vue.js
- **State Management:** Pinia

## API Endpoints

### User Management
- **Get Current User**
    - **Endpoint:** `GET /api/user`
    - **Description:** Retrieve the currently authenticated user's information.

### Parts Management
#### Team Parts
- **Get Team Parts**
    - **Endpoint:** `GET /api/parts/team/{team_id}`
    - **Description:** Retrieve parts associated with a specific team.

- **Associate Team Parts**
    - **Endpoint:** `POST /api/parts/team/{team_id}/associate`
    - **Description:** Associate parts with a team using the provided part IDs.

- **Upload Parts**
    - **Endpoint:** `POST /api/parts/upload`
    - **Description:** Upload parts from a CSV file and process them in the background.

#### Team Part Pricing
- **Update Team Part Pricing**
    - **Endpoint:** `POST /api/parts/team/upload`
    - **Description:** Update team part pricing based on the CSV file provided.

## Queue Management
To process queued jobs for CSV imports, run the following command:
```bash
php artisan queue:work
