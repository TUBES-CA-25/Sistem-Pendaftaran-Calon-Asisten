/**
 * AJAX Helper Module
 * Provides wrapper functions for making AJAX requests
 */

/**
 * Make POST request
 * @param {string} url - URL to send request to
 * @param {Object} data - Data to send
 * @returns {Promise<Object>} Response data
 */
export async function post(url, data) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('POST request failed:', error);
        throw error;
    }
}

/**
 * Make GET request
 * @param {string} url - URL to send request to
 * @returns {Promise<Object>} Response data
 */
export async function get(url) {
    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('GET request failed:', error);
        throw error;
    }
}

/**
 * Make PUT request
 * @param {string} url - URL to send request to
 * @param {Object} data - Data to send
 * @returns {Promise<Object>} Response data
 */
export async function put(url, data) {
    try {
        const response = await fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('PUT request failed:', error);
        throw error;
    }
}

/**
 * Make DELETE request
 * @param {string} url - URL to send request to
 * @returns {Promise<Object>} Response data
 */
export async function del(url) {
    try {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('DELETE request failed:', error);
        throw error;
    }
}

export default { post, get, put, del };
