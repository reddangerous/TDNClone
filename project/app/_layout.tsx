import { useEffect } from 'react';
import { Stack } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import { useFrameworkReady } from '@/hooks/useFrameworkReady';
import { QueryClientProvider } from '@tanstack/react-query';
import { LikedPeopleProvider } from '@/state/LikedPeopleContext';
import { queryClient } from '@/constants/queryClient';

export default function RootLayout() {
  useFrameworkReady();

  return (
    <LikedPeopleProvider>
      <QueryClientProvider client={queryClient}>
        <Stack screenOptions={{ headerShown: false }}>
          <Stack.Screen name="splash" options={{ headerShown: false }} />
          <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
          <Stack.Screen name="+not-found" />
        </Stack>
        <StatusBar style="auto" />
      </QueryClientProvider>
    </LikedPeopleProvider>
  );
}
