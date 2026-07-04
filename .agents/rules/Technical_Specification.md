# Technical Specification: JWT & Refresh Token Flow Correction

## Executive Summary
This specification addresses architectural flaws in the emission and validation of Refresh Tokens in the backend, specifically concerning Cross-Origin Resource Sharing (CORS) environments where the frontend and API are on different origins. The goal is to implement industry-standard security practices for JWT and ensure the frontend can successfully renew sessions without being blocked by modern browsers.

## Requirements

### Functional Requirements
- **SameSite Configuration:** The `refreshToken` cookie generated in `AuthController.cs` must adopt `SameSiteMode.None` with `Secure = true` to support cross-origin requests.
- **CORS Configuration:** The backend CORS policy must explicitly call `.AllowCredentials()`.
- **Origin Whitelisting:** Generic wildcards like `.AllowAnyOrigin()` must be removed from the credentials policy. Only explicitly trusted origins (e.g., using `.WithOrigins()`) are allowed.
- **Response Format:** The `/Auth/refresh` endpoint must exclusively return the new `AccessToken` and the new `refreshToken` cookie. It must **not** include the `User` (Profile) entity in the response payload.

### Non-Functional Requirements
- **Security:** Maintain strict compliance with secure cookie transmission to prevent CSRF and session hijacking in cross-domain contexts.
- **Compatibility:** Ensure compatibility with modern browser policies regarding cross-site requests and third-party cookies.

## Architecture & Tech Stack
- **Framework:** Laravel / PHP
- **Components to Create/Modify:**
  - **Controllers (`AuthController.cs`):** Update the `CookieOptions` used when appending the `refreshToken` to the response cookies. Ensure `SameSite = SameSiteMode.None` and `Secure = true` are applied correctly.
  - **Startup/Configuration (`routes/api.php` or `AppServiceProvider.php`):** Revise the CORS policy configuration. Remove `.AllowAnyOrigin()`, add `.AllowCredentials()`, and define allowed origins using `.WithOrigins()` based on environment configurations.
  - **DTOs:** Verify that the response DTO for the refresh endpoint remains lightweight, delivering only the access token data without the User profile payload.

## Data Flow & State
1. **Login/Refresh Request:** The frontend makes a request to `POST /Auth/login` or `POST /Auth/refresh` using `fetch` with `credentials: "include"`.
2. **CORS Preflight:** The browser may send an OPTIONS request. The backend responds with the allowed origins and `Access-Control-Allow-Credentials: true`.
3. **Cookie Emission:** The backend processes the request and returns a `Set-Cookie` header containing the `refreshToken` with flags `Secure; HttpOnly; SameSite=None`.
4. **Token Consumption:** The frontend receives the new `AccessToken` in the response body. The browser stores the `refreshToken` cookie and automatically includes it in subsequent requests to the backend (e.g., the next refresh call), provided the CORS and SameSite policies align.
