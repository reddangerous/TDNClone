import { View, Text, StyleSheet } from 'react-native';
import { Heart } from 'lucide-react-native';

export default function Logo() {
  return (
    <View style={styles.container}>
      <Heart size={80} color="#FFFFFF" fill="#FFFFFF" strokeWidth={1.5} />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    alignItems: 'center',
    justifyContent: 'center',
  },
});
