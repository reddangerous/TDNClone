import { Person, SwipeAction } from '../types/person';
import { tokenStorage } from './tokenStorage';

// API URL from environment or default
const API_BASE_URL = process.env.EXPO_PUBLIC_API_URL || 'http://192.168.2.53:8000/api/v1';

// Add request timeout
const TIMEOUT = 10000; // 10 seconds

const fetchWithTimeout = async (url: string, options: RequestInit = {}) => {
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), TIMEOUT);

  try {
    const response = await fetch(url, {
      ...options,
      signal: controller.signal,
    });
    clearTimeout(timeoutId);
    return response;
  } catch (error) {
    clearTimeout(timeoutId);
    throw error;
  }
};

// Get auth token from storage
const getAuthToken = async (): Promise<string | null> => {
  try {
    return await tokenStorage.getItem('auth_token');
  } catch (error) {
    console.error('Error retrieving auth token:', error);
    return null;
  }
};

// Store auth token in storage
const setAuthToken = async (token: string): Promise<void> => {
  try {
    await tokenStorage.setItem('auth_token', token);
  } catch (error) {
    console.error('Error storing auth token:', error);
  }
};

// Remove auth token
const removeAuthToken = async (): Promise<void> => {
  try {
    await tokenStorage.removeItem('auth_token');
  } catch (error) {
    console.error('Error removing auth token:', error);
  }
};

// Build headers with auth token
const getAuthHeaders = async (): Promise<HeadersInit> => {
  const token = await getAuthToken();
  const headers: HeadersInit = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };

  if (token) {
    (headers as any)['Authorization'] = `Bearer ${token}`;
  }

  return headers;
};

export const auth = {
  async register(name: string, email: string, password: string): Promise<{ token: string; user: any }> {
    try {
      console.log('[Auth] Registering user:', email);
      const response = await fetchWithTimeout(`${API_BASE_URL}/auth/register`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          name,
          email,
          password,
          password_confirmation: password,
        }),
      });

      if (!response.ok) {
        const error = await response.json();
        console.error('[Auth] Register error:', error);
        throw new Error(error.message || `HTTP ${response.status}: Registration failed`);
      }

      const result = await response.json();
      console.log('[Auth] Registration successful');

      // Store token
      await setAuthToken(result.token);

      return {
        token: result.token,
        user: result.user,
      };
    } catch (error) {
      console.error('[Auth] Registration error:', error);
      throw new Error(`Registration failed: ${error instanceof Error ? error.message : 'Unknown error'}`);
    }
  },

  async login(email: string, password: string): Promise<{ token: string; user: any }> {
    try {
      console.log('[Auth] Logging in user:', email);
      const response = await fetchWithTimeout(`${API_BASE_URL}/auth/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          email,
          password,
        }),
      });

      if (!response.ok) {
        const error = await response.json();
        console.error('[Auth] Login error:', error);
        throw new Error(error.message || `HTTP ${response.status}: Login failed`);
      }

      const result = await response.json();
      console.log('[Auth] Login successful');

      // Store token
      await setAuthToken(result.token);

      return {
        token: result.token,
        user: result.user,
      };
    } catch (error) {
      console.error('[Auth] Login error:', error);
      throw new Error(`Login failed: ${error instanceof Error ? error.message : 'Unknown error'}`);
    }
  },

  async logout(): Promise<void> {
    try {
      console.log('[Auth] Logging out');
      const headers = await getAuthHeaders();
      const response = await fetchWithTimeout(`${API_BASE_URL}/auth/logout`, {
        method: 'POST',
        headers,
      });

      if (!response.ok) {
        console.warn('[Auth] Logout API error, clearing local token');
      }

      // Remove token locally regardless of server response
      await removeAuthToken();
      console.log('[Auth] Logout successful');
    } catch (error) {
      console.error('[Auth] Logout error:', error);
      // Still remove token locally
      await removeAuthToken();
    }
  },

  async getCurrentUser(): Promise<any> {
    try {
      console.log('[Auth] Fetching current user');
      const headers = await getAuthHeaders();
      const response = await fetchWithTimeout(`${API_BASE_URL}/auth/user`, {
        method: 'GET',
        headers,
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: Failed to fetch user`);
      }

      const user = await response.json();
      console.log('[Auth] Got current user:', user.email);
      return user;
    } catch (error) {
      console.error('[Auth] Get user error:', error);
      throw error;
    }
  },

  async isAuthenticated(): Promise<boolean> {
    const token = await getAuthToken();
    return !!token;
  },
};

export const api = {
  async getRecommendedPeople(page: number = 1, limit: number = 10): Promise<{ data: Person[], total: number }> {
    try {
      console.log(`[API] Fetching recommended people from: ${API_BASE_URL}/people?page=${page}&per_page=${limit}`);
      const headers = await getAuthHeaders();
      const response = await fetchWithTimeout(`${API_BASE_URL}/people?page=${page}&per_page=${limit}`, {
        headers,
      });
      
      if (!response.ok) {
        console.error(`[API] Error response: ${response.status} ${response.statusText}`);
        throw new Error(`HTTP ${response.status}: Failed to fetch recommended people`);
      }
      
      const result = await response.json();
      console.log('[API] Successfully fetched people:', result.data?.length || 0, 'records');
      
      // Handle Laravel pagination response format
      return {
        data: (result.data || []).map((person: any) => ({
          id: String(person.id),
          name: person.name,
          age: person.age,
          location: person.location,
          pictures: person.pictures && Array.isArray(person.pictures) ? person.pictures : [],
        })),
        total: result.total || 0,
      };
    } catch (error) {
      console.error('[API] Fetch error:', error);
      throw new Error(`Failed to fetch recommended people: ${error instanceof Error ? error.message : 'Unknown error'}`);
    }
  },

  async likePerson(personId: string): Promise<void> {
    try {
      console.log(`[API] Liking person: ${personId}`);
      const headers = await getAuthHeaders();
      const response = await fetchWithTimeout(`${API_BASE_URL}/likes/like`, {
        method: 'POST',
        headers,
        body: JSON.stringify({ 
          person_id: parseInt(personId),
        }),
      });
      
      if (!response.ok) {
        console.error(`[API] Error liking person: ${response.status}`);
        throw new Error(`HTTP ${response.status}: Failed to like person`);
      }
      
      console.log('[API] Successfully liked person');
    } catch (error) {
      console.error('[API] Like error:', error);
      throw new Error(`Failed to like person: ${error instanceof Error ? error.message : 'Unknown error'}`);
    }
  },

  async dislikePerson(personId: string): Promise<void> {
    try {
      console.log(`[API] Disliking person: ${personId}`);
      const headers = await getAuthHeaders();
      const response = await fetchWithTimeout(`${API_BASE_URL}/likes/dislike`, {
        method: 'POST',
        headers,
        body: JSON.stringify({ 
          person_id: parseInt(personId),
        }),
      });
      
      if (!response.ok) {
        console.error(`[API] Error disliking person: ${response.status}`);
        throw new Error(`HTTP ${response.status}: Failed to dislike person`);
      }
      
      console.log('[API] Successfully disliked person');
    } catch (error) {
      console.error('[API] Dislike error:', error);
      throw new Error(`Failed to dislike person: ${error instanceof Error ? error.message : 'Unknown error'}`);
    }
  },

  async getLikedPeople(page: number = 1, limit: number = 20): Promise<{ data: Person[], total: number }> {
    try {
      console.log(`[API] Fetching liked people from: ${API_BASE_URL}/likes/liked-people?page=${page}&per_page=${limit}`);
      const headers = await getAuthHeaders();
      const response = await fetchWithTimeout(`${API_BASE_URL}/likes/liked-people?page=${page}&per_page=${limit}`, {
        method: 'GET',
        headers,
      });
      
      if (!response.ok) {
        console.error(`[API] Error fetching liked people: ${response.status}`);
        throw new Error(`HTTP ${response.status}: Failed to fetch liked people`);
      }
      
      const result = await response.json();
      console.log('[API] Successfully fetched liked people:', result.data?.length || 0, 'records');
      
      // Handle Laravel pagination response and extract person data from likes
      return {
        data: (result.data || []).map((like: any) => ({
          id: String(like.person.id),
          name: like.person.name,
          age: like.person.age,
          location: like.person.location,
          pictures: like.person.pictures && Array.isArray(like.person.pictures) ? like.person.pictures : [],
        })),
        total: result.total || 0,
      };
    } catch (error) {
      console.error('[API] Fetch error:', error);
      throw new Error(`Failed to fetch liked people: ${error instanceof Error ? error.message : 'Unknown error'}`);
    }
  },

  // Health check to verify API is reachable
  async healthCheck(): Promise<boolean> {
    try {
      console.log(`[API] Running health check on: ${API_BASE_URL.replace('/api/v1', '')}/health`);
      const response = await fetchWithTimeout(`${API_BASE_URL.replace('/api/v1', '')}/health`, {
        method: 'GET',
      });
      return response.ok;
    } catch (error) {
      console.error('[API] Health check failed:', error);
      return false;
    }
  },
};
