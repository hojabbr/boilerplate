import { create } from 'zustand';

interface PreferencesState {
    sidebarCollapsed: boolean;
    setSidebarCollapsed: (collapsed: boolean) => void;
}

export const usePreferencesStore = create<PreferencesState>((set) => ({
    sidebarCollapsed: false,
    setSidebarCollapsed: (sidebarCollapsed) => set({ sidebarCollapsed }),
}));
