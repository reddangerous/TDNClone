import { TouchableOpacity, StyleSheet, View } from 'react-native';
import { LucideIcon } from 'lucide-react-native';

interface ActionButtonProps {
  icon: LucideIcon;
  size?: number;
  iconSize?: number;
  color: string;
  backgroundColor?: string;
  onPress: () => void;
}

export default function ActionButton({
  icon: Icon,
  size = 64,
  iconSize = 32,
  color,
  backgroundColor = '#FFFFFF',
  onPress,
}: ActionButtonProps) {
  return (
    <TouchableOpacity onPress={onPress}>
      <View style={[styles.button, { width: size, height: size, backgroundColor }]}>
        <Icon size={iconSize} color={color} strokeWidth={2.5} />
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  button: {
    borderRadius: 100,
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.15,
    shadowRadius: 4,
    elevation: 3,
  },
});
