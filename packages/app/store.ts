import { configureStore } from '@reduxjs/toolkit'
import AuthReducer from 'slices/auth'
import SidebarReducer from 'slices/sidebar'

export const store = configureStore({
  reducer: {
    auth: AuthReducer,
    sidebar: SidebarReducer,
  },
})

export type RootState = ReturnType<typeof store.getState>
export type AppDispatch = typeof store.dispatch
