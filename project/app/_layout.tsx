import { useEffect, useState } from 'react';
import { Stack } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import { useFrameworkReady } from '@/hooks/useFrameworkReady';
import { QueryClientProvider } from '@tanstack/react-query';
import { LikedPeopleProvider } from '@/state/LikedPeopleContext';
import { queryClient } from '@/constants/queryClient';
import { auth } from '@/services/api';
import { tokenStorage } from '@/services/tokenStorage';

function SplashLayout() {
  return (
    <Stack screenOptions={{ headerShown: false }}>
      <Stack.Screen name="splash" options={{ headerShown: false }} />
    </Stack>
  );
}

function AuthLayout() {
  return (
    <Stack screenOptions={{ headerShown: false }}>
      <Stack.Screen name="login" options={{ headerShown: false }} />
      <Stack.Screen name="signup" options={{ headerShown: false }} />
      <Stack.Screen name="+not-found" />
    </Stack>
  );
}

function AppLayout() {
  return (
    <Stack screenOptions={{ headerShown: false }}>
      <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
      <Stack.Screen name="+not-found" />
    </Stack>
  );
}

export default function RootLayout() {
  useFrameworkReady();
  const [isLoading, setIsLoading] = useState(true);
  const [isAuthenticated, setIsAuthenticated] = useState(false);

  useEffect(() => {
    bootstrapAsync();
  }, []);

  const bootstrapAsync = async () => {
    try {
      // Check if user is already logged in
      const token = await tokenStorage.getItem('auth_token');
      if (token) {
        // Verify token is still valid by fetching current user
        try {
          await auth.getCurrentUser();
          setIsAuthenticated(true);
        } catch (error) {
          // Token is invalid, clear it
          await tokenStorage.removeItem('auth_token');
          setIsAuthenticated(false);
        }
      }
    } catch (error) {
      console.error('Bootstrap error:', error);
    } finally {
      setIsLoading(false);
    }
  };

  let content;

  if (isLoading) {
    content = <SplashLayout />;
  } else if (isAuthenticated) {
    content = <AppLayout />;
  } else {
    content = <AuthLayout />;
  }

  return (
    <LikedPeopleProvider>
      <QueryClientProvider client={queryClient}>
        {content}
        <StatusBar style="auto" />
      </QueryClientProvider>
    </LikedPeopleProvider>
  );
}
