import { create } from 'zustand';

interface UIState {
    modals: Record<string, boolean>;
    setModalOpen: (key: string, open: boolean) => void;
    alert: { message: string; variant?: 'default' | 'destructive' } | null;
    setAlert: (alert: UIState['alert']) => void;
}

export const useUIStore = create<UIState>((set) => ({
    modals: {},
    setModalOpen: (key, open) =>
        set((state) => ({
            modals: { ...state.modals, [key]: open },
        })),
    alert: null,
    setAlert: (alert) => set({ alert }),
}));
