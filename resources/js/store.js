import { configureStore } from '@reduxjs/toolkit'
import {userSlice} from "@/reducers/reducers.js";

export default configureStore({
    reducer: {
        user: userSlice.reducer
    }
})
