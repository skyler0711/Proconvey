import { createSlice, PayloadAction } from '@reduxjs/toolkit'
import { User } from 'gql/graphql'
import { Subset } from 'types/subset'

type InternalUser = Subset<User>

export interface AuthState {
  authenticated: boolean
  authChecked: boolean
  confirmedPassword: boolean
  user?: InternalUser
  isLoading: boolean
}

const initialState: AuthState = {
  authenticated: false,
  authChecked: false,
  confirmedPassword: false,
  user: undefined,
  isLoading: true,
}

export const auth = createSlice({
  name: 'auth',
  initialState,
  reducers: {
    login: (state, action: PayloadAction<InternalUser>) => {
      state.user = action.payload
      state.authenticated = true
      state.isLoading = false
    },
    loginViaSession: (state, action: PayloadAction<InternalUser>) => {
      state.user = action.payload
      state.authenticated = true
      state.isLoading = false
    },
    logout: (state) => {
      state.user = undefined
      state.authenticated = false
    },
    setConfirmedPassword: (state, action: PayloadAction<boolean>) => {
      state.confirmedPassword = action.payload
    },
    updateAuthUser: (state, action: PayloadAction<InternalUser>) => {
      state.user = Object.assign(state.user ?? {}, action.payload) as InternalUser
    },
  },
})

export const {
  login,
  loginViaSession,
  logout,
  setConfirmedPassword,
  updateAuthUser,
} = auth.actions

export default auth.reducer
