import { StyleSheet, Dimensions, PanResponder, Animated as RNAnimated, View, Text } from 'react-native';
import { useRef, useEffect } from 'react';
import PersonCard from '../molecules/PersonCard';
import { Person } from '../../types/person';

const { width: SCREEN_WIDTH } = Dimensions.get('window');
const SWIPE_THRESHOLD = SCREEN_WIDTH * 0.3;

interface SwipeableCardProps {
  person: Person;
  onSwipeLeft: () => void;
  onSwipeRight: () => void;
  index: number;
}

export default function SwipeableCard({
  person,
  onSwipeLeft,
  onSwipeRight,
  index,
}: SwipeableCardProps) {
  const pan = useRef(new RNAnimated.ValueXY()).current;
  const opacity = useRef(new RNAnimated.Value(1)).current;
  const scale = useRef(new RNAnimated.Value(1 - index * 0.05)).current;

  const panResponder = useRef(
    PanResponder.create({
      onStartShouldSetPanResponder: () => true,
      onMoveShouldSetPanResponder: () => true,
      onPanResponderMove: RNAnimated.event([null, { dx: pan.x, dy: pan.y }], {
        useNativeDriver: false,
      }),
      onPanResponderRelease: (evt, gestureState) => {
        if (Math.abs(gestureState.dx) > SWIPE_THRESHOLD) {
          const direction = gestureState.dx > 0 ? 1 : -1;
          RNAnimated.parallel([
            RNAnimated.timing(pan.x, {
              toValue: direction * SCREEN_WIDTH * 1.5,
              duration: 300,
              useNativeDriver: false,
            }),
            RNAnimated.timing(pan.y, {
              toValue: gestureState.dy,
              duration: 300,
              useNativeDriver: false,
            }),
            RNAnimated.timing(opacity, {
              toValue: 0,
              duration: 300,
              useNativeDriver: false,
            }),
          ]).start(() => {
            if (direction > 0) {
              onSwipeRight();
            } else {
              onSwipeLeft();
            }
          });
        } else {
          RNAnimated.spring(pan, {
            toValue: { x: 0, y: 0 },
            useNativeDriver: false,
          }).start();
        }
      },
    })
  ).current;

  const rotate = pan.x.interpolate({
    inputRange: [-SCREEN_WIDTH / 2, 0, SCREEN_WIDTH / 2],
    outputRange: ['-15deg', '0deg', '15deg'],
  });

  const likeOpacity = pan.x.interpolate({
    inputRange: [0, SWIPE_THRESHOLD],
    outputRange: [0, 1],
  });

  const nopeOpacity = pan.x.interpolate({
    inputRange: [-SWIPE_THRESHOLD, 0],
    outputRange: [1, 0],
  });

  return (
    <RNAnimated.View
      style={[
        styles.cardContainer,
        {
          transform: [
            { translateX: pan.x },
            { translateY: pan.y },
            { rotate },
            { scale },
          ],
          opacity: opacity,
        },
      ]}
      {...panResponder.panHandlers}
    >
      <PersonCard person={person} />
      <RNAnimated.View style={[styles.overlay, styles.likeOverlay, { opacity: likeOpacity }]}>
        <Text style={styles.overlayText}>LIKE</Text>
      </RNAnimated.View>
      <RNAnimated.View style={[styles.overlay, styles.nopeOverlay, { opacity: nopeOpacity }]}>
        <Text style={styles.overlayText}>NOPE</Text>
      </RNAnimated.View>
    </RNAnimated.View>
  );
}

const styles = StyleSheet.create({
  cardContainer: {
    position: 'absolute',
    width: SCREEN_WIDTH * 0.92,
  },
  overlay: {
    position: 'absolute',
    top: 50,
    paddingHorizontal: 20,
    paddingVertical: 10,
    borderWidth: 4,
    borderRadius: 8,
  },
  likeOverlay: {
    left: 50,
    borderColor: '#4CAF50',
    transform: [{ rotate: '-20deg' }],
  },
  nopeOverlay: {
    right: 50,
    borderColor: '#FF5864',
    transform: [{ rotate: '20deg' }],
  },
  overlayText: {
    fontSize: 32,
    fontWeight: 'bold',
    letterSpacing: 4,
  },
});
