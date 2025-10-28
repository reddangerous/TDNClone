/**
 * Token Storage Service
 * Handles token persistence across app restarts
 * Uses AsyncStorage if available, falls back to memory
 */

let memoryStorage: { [key: string]: string } = {};
let AsyncStorageModule: any = null;

// Try to load AsyncStorage if available
try {
  const AsyncStorage = require('@react-native-async-storage/async-storage').default;
  AsyncStorageModule = AsyncStorage;
} catch (e) {
  // AsyncStorage not available, will use memory
  console.log('AsyncStorage not available, using memory storage');
}

export const tokenStorage = {
  async getItem(key: string): Promise<string | null> {
    if (AsyncStorageModule) {
      try {
        return await AsyncStorageModule.getItem(key);
      } catch (error) {
        console.error('AsyncStorage getItem error:', error);
        return memoryStorage[key] || null;
      }
    }
    return memoryStorage[key] || null;
  },

  async setItem(key: string, value: string): Promise<void> {
    memoryStorage[key] = value;
    if (AsyncStorageModule) {
      try {
        await AsyncStorageModule.setItem(key, value);
      } catch (error) {
        console.error('AsyncStorage setItem error:', error);
      }
    }
  },

  async removeItem(key: string): Promise<void> {
    delete memoryStorage[key];
    if (AsyncStorageModule) {
      try {
        await AsyncStorageModule.removeItem(key);
      } catch (error) {
        console.error('AsyncStorage removeItem error:', error);
      }
    }
  },
};
