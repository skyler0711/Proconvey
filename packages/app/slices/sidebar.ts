import { createSlice, PayloadAction } from '@reduxjs/toolkit'

export interface SidebarState {
  isSidebarOpen: boolean
}

const initialState: SidebarState = {
  isSidebarOpen: false,
}

export const auth = createSlice({
  name: 'auth',
  initialState,
  reducers: {
    setSidebarOpen: (state, action: PayloadAction<boolean>) => {
      state.isSidebarOpen = action.payload
    },
  },
})

export const {
  setSidebarOpen,
} = auth.actions

export default auth.reducer
