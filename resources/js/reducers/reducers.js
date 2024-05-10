import { createAsyncThunk, createSlice } from '@reduxjs/toolkit';
import axios from 'axios';

export const fetchUserData = createAsyncThunk(
    'user/fetchUserData',
    async () => {
        try {
            const response = await axios.post('/api/me', {}, {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('apiToken')
                }
            });
            return await response.data.user;
        } catch (error) {
            // Handle any errors
            console.error('Error fetching user data:', error);
            throw error;
        }
    }
);

const initialState = {
    userData: null,
    isLoading: false,
    error: null
};

export const userSlice = createSlice({
    name: 'user',
    initialState,
    extraReducers: (builder) => {
        builder
            .addCase(fetchUserData.pending, (state) => {
                state.isLoading = true;
                state.error = null;
            })
            .addCase(fetchUserData.fulfilled, (state, action) => {
                state.userData = action.payload;
                state.isLoading = false;
                state.error = null;
            })
            .addCase(fetchUserData.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.error.message;
            });
    }
});

export const userActions = {
    fetchUserData
};

export default userSlice.reducer;

