# Technical Specification: Keyset Pagination in MeetingMinute

## Executive Summary
This specification defines the implementation of Keyset Pagination (Cursor Pagination) for the `MeetingMinute` entity retrieval to enhance performance on large datasets.

## Requirements

### Functional Requirements
- **Cursor Implementation:** The API must support cursor-based pagination using the entity's `Id` as the keyset, ordered in descending order (newest to oldest).
- **Query Parameters:** The endpoint must accept `cursor` (the ID of the last item in the previous page) and `pageSize` (default to 5).
- **Response Structure:** The response payload must include the data array, a boolean `HasNextPage`, and the `NextCursor` integer to fetch the next page.
- **N+1 Prevention:** Any related entities fetched during the process must use `Include()` as per project conventions.

### Non-Functional Requirements
- **Performance:** Queries must utilize indexes efficiently. The filter `WHERE Id < @cursor` must rely on the primary key index.
- **Memory Optimization:** `toBase()` must be strictly used for this read-only query.
- **Async Execution:** Eloquent `get()` must be used.

## Architecture & Tech Stack
- **Framework:** Laravel / Eloquent
- **Components to Create/Modify:**
  - **Models/DTOs:** Create a `PagedResponse<T>` generic model.
  - **Controllers (`MeetingMinuteController.cs`):** Update the `GET api/MeetingMinute/` endpoint to accept query parameters and return the `PagedResponse`.
  - **Services (`MeetingMinuteService.cs`):** Implement the keyset query logic.

## Data Flow & State
1. **Initial Request:** Client requests `GET /api/MeetingMinute?pageSize=5`.
2. **Backend Processing:** Service queries the database `ORDER BY Id DESC LIMIT 6`.
3. **Response Validation:** If 6 records are returned, the first 5 are sent to the client, `HasNextPage` is set to `true`, and `NextCursor` is set to the `Id` of the 5th record.
4. **Subsequent Request:** Client requests `GET /api/MeetingMinute?cursor={NextCursor}&pageSize=5`. Backend processes `WHERE Id < {NextCursor} ORDER BY Id DESC LIMIT 6`.
