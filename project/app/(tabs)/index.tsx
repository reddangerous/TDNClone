import { View, Text, StyleSheet, ActivityIndicator, Alert, TouchableOpacity } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useQuery, useMutation } from '@tanstack/react-query';
import { useLikedPeople } from '@/state/LikedPeopleContext';
import { X, Heart, AlertCircle } from 'lucide-react-native';
import { api } from '@/services/api';
import SwipeableCard from '@/components/organisms/SwipeableCard';
import ActionButton from '@/components/atoms/ActionButton';
import { useState, useEffect } from 'react';
import { Person } from '@/types/person';

export default function HomeScreen() {
  const [currentIndex, setCurrentIndex] = useState(0);
  const [apiError, setApiError] = useState<string | null>(null);
  const { likedPeople, addLikedPerson } = useLikedPeople();

  const { data, isLoading, error, refetch, isRefetching } = useQuery({
    queryKey: ['recommendedPeople'],
    queryFn: () => api.getRecommendedPeople(1, 10),
    retry: 2,
    retryDelay: 1000,
  });

  const likeMutation = useMutation({
    mutationFn: (personId: string) => api.likePerson(personId),
    onError: (error: any) => {
      Alert.alert('Error', error.message || 'Failed to like person');
    },
  });

  const dislikeMutation = useMutation({
    mutationFn: (personId: string) => api.dislikePerson(personId),
    onError: (error: any) => {
      Alert.alert('Error', error.message || 'Failed to dislike person');
    },
  });

  // Set error message when query fails
  useEffect(() => {
    if (error) {
      setApiError((error as any).message || 'Failed to fetch people');
    } else {
      setApiError(null);
    }
  }, [error]);

  const handleSwipeLeft = () => {
    const person = data?.data[currentIndex];
    if (person) {
      dislikeMutation.mutate(person.id);
      setCurrentIndex((prev) => prev + 1);
    }
  };

  const handleSwipeRight = () => {
    const person = data?.data[currentIndex];
    if (person) {
      likeMutation.mutate(person.id);
      addLikedPerson(person);
      setCurrentIndex((prev) => prev + 1);
    }
  };

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#FF5864" />
        <Text style={styles.loadingText}>Connecting to server...</Text>
      </View>
    );
  }

  if (apiError) {
    return (
      <SafeAreaView style={styles.container} edges={['top']}>
        <View style={styles.header}>
          <Text style={styles.logo}>tinder</Text>
        </View>
        <View style={styles.errorContainer}>
          <AlertCircle color="#FF5864" size={64} />
          <Text style={styles.errorTitle}>Connection Error</Text>
          <Text style={styles.errorText}>{apiError}</Text>
          <TouchableOpacity 
            style={styles.retryButton}
            onPress={() => {
              setApiError(null);
              refetch();
            }}
          >
            <Text style={styles.retryButtonText}>Retry</Text>
          </TouchableOpacity>
        </View>
      </SafeAreaView>
    );
  }

  const currentPerson = data?.data[currentIndex];
  const hasMoreCards = currentIndex < (data?.data.length || 0);

  return (
    <SafeAreaView style={styles.container} edges={['top']}>
      <View style={styles.header}>
        <Text style={styles.logo}>tinder</Text>
      </View>

      <View style={styles.cardContainer}>
        {!hasMoreCards ? (
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyText}>No more people to show</Text>
            <Text style={styles.emptySubtext}>Check back later for new matches</Text>
          </View>
        ) : (
          <>
            {data?.data
              .slice(currentIndex, currentIndex + 3)
              .reverse()
              .map((person, idx) => (
                <SwipeableCard
                  key={person.id}
                  person={person}
                  onSwipeLeft={handleSwipeLeft}
                  onSwipeRight={handleSwipeRight}
                  index={data.data.length - currentIndex - idx - 1}
                />
              ))}
          </>
        )}
      </View>

      {hasMoreCards && (
        <View style={styles.actionsContainer}>
          <ActionButton
            icon={X}
            color="#FF5864"
            iconSize={36}
            size={70}
            onPress={handleSwipeLeft}
          />
          <ActionButton
            icon={Heart}
            color="#4CAF50"
            iconSize={36}
            size={70}
            onPress={handleSwipeRight}
          />
        </View>
      )}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FAFAFA',
  },
  loadingContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  loadingText: {
    fontSize: 16,
    color: '#666',
    marginTop: 12,
  },
  header: {
    paddingHorizontal: 20,
    paddingVertical: 16,
  },
  logo: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#FF5864',
  },
  cardContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 10,
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    padding: 40,
  },
  errorContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    padding: 40,
  },
  errorTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#FF5864',
    marginTop: 16,
    marginBottom: 8,
  },
  errorText: {
    fontSize: 16,
    color: '#666',
    textAlign: 'center',
    marginBottom: 24,
    lineHeight: 24,
  },
  retryButton: {
    backgroundColor: '#FF5864',
    paddingHorizontal: 32,
    paddingVertical: 12,
    borderRadius: 8,
  },
  retryButtonText: {
    color: '#FFF',
    fontSize: 16,
    fontWeight: 'bold',
  },
  emptyText: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 8,
    textAlign: 'center',
  },
  emptySubtext: {
    fontSize: 16,
    color: '#666',
    textAlign: 'center',
  },
  actionsContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    gap: 40,
    paddingVertical: 20,
    paddingBottom: 30,
  },
});
