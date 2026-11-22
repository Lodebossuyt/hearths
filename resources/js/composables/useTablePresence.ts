import { echo } from '@laravel/echo-vue';
import { onBeforeUnmount, onMounted, ref } from 'vue';

type Member = { id: number; name: string };

/**
 * Tracks which seated Players currently have the Room open, via a presence channel
 * kept separate from the channel live props listen on.
 */
export function useTablePresence(roomCode: string, enabled: boolean) {
    const connectedUserIds = ref<Set<number>>(new Set());
    const hasConnected = ref(false);
    const channelName = `table.${roomCode}`;

    onMounted(() => {
        if (!enabled) {
            return;
        }

        echo()
            .join(channelName)
            .here((members: Member[]) => {
                connectedUserIds.value = new Set(
                    members.map((member) => member.id),
                );
                hasConnected.value = true;
            })
            .joining((member: Member) => {
                connectedUserIds.value = new Set([
                    ...connectedUserIds.value,
                    member.id,
                ]);
            })
            .leaving((member: Member) => {
                const remaining = new Set(connectedUserIds.value);
                remaining.delete(member.id);
                connectedUserIds.value = remaining;
            });
    });

    onBeforeUnmount(() => {
        if (enabled) {
            echo().leave(channelName);
        }
    });

    function isAway(userId: number): boolean {
        return hasConnected.value && !connectedUserIds.value.has(userId);
    }

    return { isAway };
}
