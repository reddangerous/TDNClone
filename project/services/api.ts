import { Person, SwipeAction } from '../types/person';

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

export const api = {
  async getRecommendedPeople(page: number = 1, limit: number = 10): Promise<{ data: Person[], total: number }> {
    try {
      console.log(`[API] Fetching recommended people from: ${API_BASE_URL}/people?page=${page}&per_page=${limit}`);
      const response = await fetchWithTimeout(`${API_BASE_URL}/people?page=${page}&per_page=${limit}`);
      
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
      const response = await fetchWithTimeout(`${API_BASE_URL}/likes/like`, {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ 
          person_id: parseInt(personId),
          user_id: 1, // Using test user ID 1 from seeder
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
      const response = await fetchWithTimeout(`${API_BASE_URL}/likes/dislike`, {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ 
          person_id: parseInt(personId),
          user_id: 1, // Using test user ID 1 from seeder
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
      const response = await fetchWithTimeout(`${API_BASE_URL}/likes/liked-people?page=${page}&per_page=${limit}&user_id=1`, {
        method: 'GET',
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

export const mockApi = {
  async getRecommendedPeople(page: number = 1, limit: number = 10): Promise<{ data: Person[], total: number }> {
    await new Promise(resolve => setTimeout(resolve, 500));

    const mockPeople: Person[] = [
      {
        id: '1',
        name: 'Sarah',
        age: 28,
        pictures: ['https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg'],
        location: '2km away',
      },
      {
        id: '2',
        name: 'Emma',
        age: 25,
        pictures: ['https://images.pexels.com/photos/1239291/pexels-photo-1239291.jpeg'],
        location: '5km away',
      },
      {
        id: '3',
        name: 'Jessica',
        age: 30,
        pictures: ['https://images.pexels.com/photos/1181686/pexels-photo-1181686.jpeg'],
        location: '3km away',
      },
      {
        id: '4',
        name: 'Olivia',
        age: 27,
        pictures: ['https://images.pexels.com/photos/1130626/pexels-photo-1130626.jpeg'],
        location: '7km away',
      },
      {
        id: '5',
        name: 'Sophia',
        age: 26,
        pictures: ['https://images.pexels.com/photos/1181690/pexels-photo-1181690.jpeg'],
        location: '4km away',
      },
    ];

    const start = (page - 1) * limit;
    const end = start + limit;

    return {
      data: mockPeople.slice(start, end),
      total: mockPeople.length,
    };
  },

  async likePerson(personId: string): Promise<void> {
    await new Promise(resolve => setTimeout(resolve, 300));
    console.log('Liked person:', personId);
  },

  async dislikePerson(personId: string): Promise<void> {
    await new Promise(resolve => setTimeout(resolve, 300));
    console.log('Disliked person:', personId);
  },

  async getLikedPeople(page: number = 1, limit: number = 20): Promise<{ data: Person[], total: number }> {
    await new Promise(resolve => setTimeout(resolve, 500));
    return { data: [], total: 0 };
  },
};
