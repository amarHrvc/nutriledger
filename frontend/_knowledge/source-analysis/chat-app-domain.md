# Chat App Domain Source Structure

## Overview

The Chat App domain implements a comprehensive real-time messaging application with user presence detection, contact management, message history, and responsive multi-panel layouts. Built using Redux for state management, React hooks for component logic, and MUI components for UI, it demonstrates complex state coordination across multiple interactive panels.

**Key characteristics:**
- Redux state management for chat data and user contacts
- Real-time messaging with message history
- User status tracking (online, offline, away, busy)
- Contact list with search and filtering
- Multi-panel responsive layout (sidebar, chat, profile)
- Message grouping by date
- User profile viewing
- Responsive behavior for mobile/tablet/desktop
- Redux thunks for async operations

---

## Application Structure

**Location:** `/src/views/apps/chat/`

### Core Components

1. **ChatWrapper (`index.tsx`)** - Main orchestrator
2. **SidebarLeft.tsx** - Contact list and user selection
3. **ChatContent.tsx** - Message display and user info
4. **ChatLog.tsx** - Message history rendering
5. **SendMsgForm.tsx** - Message input form
6. **UserProfileLeft.tsx** - User profile drawer (left)
7. **UserProfileRight.tsx** - User profile drawer (right)
8. **AvatarWithBadge.tsx** - Avatar with status indicator
9. **utils.ts** - Utility functions for date formatting

---

## State Management (Redux)

### Chat Slice Structure

**Location:** Redux store for chat reducer

**Store Shape:**
```typescript
interface ChatState {
  activeUser: ContactType | null
  contacts: ContactType[]
  messages: ChatMessage[]
  userProfile: UserProfile | null
  loading: boolean
  error: string | null
  searchQuery: string
  selectedConversation: string | null
}
```

**Contact Type:**
```typescript
interface ContactType {
  id: number
  fullName: string
  avatar: string
  avatarColor: ThemeColor
  role: string
  status: 'online' | 'offline' | 'away' | 'busy'
  about?: string
  joinedDate?: Date
  lastSeen?: Date
}
```

**Chat Message Type:**
```typescript
interface ChatMessage {
  id: string
  conversationId: string
  senderId: number
  receiverId: number
  message: string
  timestamp: Date
  isOwn: boolean
  attachments?: string[]
}
```

### Key Redux Actions

#### `getActiveUserData(userId: number)`
- **Type:** Redux Thunk
- **Purpose:** Fetch active user details and conversation
- **Dispatch:** Sets activeUser, loads message history
- **Usage:** Called when user clicks contact in sidebar

```typescript
const activeUser = dispatch(getActiveUserData(userId))
```

#### Message Actions
- `sendMessage(contactId, message)` - Send new message
- `loadMessages(contactId)` - Load message history
- `updateContactStatus(contactId, status)` - Update user status
- `searchContacts(query)` - Filter contacts by search

### Selectors

```typescript
// Select chat reducer
const chatStore = useSelector((state: RootState) => state.chatReducer)

// Common selections
const activeUser = chatStore.activeUser
const contacts = chatStore.contacts
const messages = chatStore.messages
const isLoading = chatStore.loading
```

---

## Component Hierarchy

### ChatWrapper (`/src/views/apps/chat/index.tsx`)

**Type:** Client Component (`'use client'`)

**Responsibilities:**
- Redux dispatch and selector management
- Responsive state management (sidebar, backdrop)
- Layout coordination across responsive breakpoints
- Event handlers for user selection

**State Management:**
```typescript
const [backdropOpen, setBackdropOpen] = useState(false)
const [sidebarOpen, setSidebarOpen] = useState(false)
const messageInputRef = useRef<HTMLDivElement>(null!)

const dispatch = useDispatch()
const chatStore = useSelector((state: RootState) => state.chatReducer)

// Responsive breakpoint hooks
const isBelowLgScreen = useMediaQuery(theme.breakpoints.down('lg'))
const isBelowMdScreen = useMediaQuery(theme.breakpoints.down('md'))
const isBelowSmScreen = useMediaQuery(theme.breakpoints.down('sm'))
```

**Key Effects:**
1. Focus message input when active user changes
2. Close backdrop when layout transitions
3. Manage sidebar visibility based on screen size
4. Sync sidebar state with backdrop

**Data Flow:**
```
Redux Store (chatReducer)
        ↓
    ChatWrapper
    ↙  ↓  ↘
SidebarLeft ChatContent UserProfile*
    ↓         ↓
Contact   Message
List      Log & Form
```

### SidebarLeft (`/src/views/apps/chat/SidebarLeft.tsx`)

**Type:** Client Component

**Props:**
```typescript
interface SidebarLeftProps {
  contacts: ContactType[]
  activeUser: ContactType | null
  onSelectUser: (userId: number) => void
  onSearchChange: (query: string) => void
  isSidebarOpen: boolean
  onClose: () => void
}
```

**Features:**
- User search with filter
- Contact list rendering
- Status indicators (online/offline/away/busy)
- Avatar display with color coding
- Responsive drawer for mobile

**Status Mapping:**
```typescript
const statusObj = {
  'online': 'success',    // Green
  'offline': 'secondary', // Gray
  'away': 'warning',      // Yellow
  'busy': 'error'         // Red
}
```

**Render Structure:**
```
<Drawer> (or fixed on desktop)
  <SearchInput />
  <ContactList>
    {contacts.map(contact =>
      <ContactItem
        key={contact.id}
        contact={contact}
        isActive={contact.id === activeUser?.id}
        onClick={() => onSelectUser(contact.id)}
      >
        <Avatar status={contact.status} />
        <UserInfo />
      </ContactItem>
    )}
  </ContactList>
</Drawer>
```

### ChatContent (`/src/views/apps/chat/ChatContent.tsx`)

**Type:** Client Component

**Props:**
```typescript
interface ChatContentProps {
  chatStore: ChatDataType
  dispatch: AppDispatch
  backdropOpen: boolean
  setBackdropOpen: (open: boolean) => void
  setSidebarOpen: (open: boolean) => void
  isBelowMdScreen: boolean
  isBelowLgScreen: boolean
  isBelowSmScreen: boolean
  messageInputRef: RefObject<HTMLDivElement>
}
```

**Responsibilities:**
- Display active user information
- Show message history
- Handle message sending
- Manage user profile view
- Control responsive panels

**Sub-components:**
- **UserAvatar** - Avatar with user info and profile trigger
- **ChatLog** - Message history display
- **SendMsgForm** - Message input
- **UserProfileRight** - Profile sidebar

**Render Structure:**
```
<ChatContent>
  <ChatHeader>
    <UserAvatar 
      onClick={() => setUserProfileLeftOpen(true)} 
    />
    <OptionMenu />
  </ChatHeader>
  <ChatLog messages={messages} />
  <SendMsgForm 
    onSend={(msg) => dispatch(sendMessage(msg))} 
    ref={messageInputRef}
  />
  <UserProfileRight 
    user={activeUser}
    open={userProfileRightOpen}
    onClose={() => setUserProfileRightOpen(false)}
  />
</ChatContent>
```

### ChatLog (`/src/views/apps/chat/ChatLog.tsx`)

**Type:** Client Component

**Purpose:** Render message history with date grouping

**Features:**
- Date-based message grouping
- Message ownership detection (own vs received)
- Different styling for sender/receiver
- Auto-scroll to latest message
- Message timestamps

**Date Grouping Logic:**
```typescript
// Messages grouped by date
const groupedMessages = useMemo(() => {
  return messages.reduce((acc, msg) => {
    const dateKey = formatDate(msg.timestamp)
    if (!acc[dateKey]) acc[dateKey] = []
    acc[dateKey].push(msg)
    return acc
  }, {} as Record<string, ChatMessage[]>)
}, [messages])
```

**Render Pattern:**
```
<div className="chat-log">
  {Object.entries(groupedMessages).map(([date, msgs]) => (
    <div key={date} className="date-group">
      <Typography>{date}</Typography>
      {msgs.map(msg => (
        <MessageBubble
          key={msg.id}
          message={msg}
          isOwn={msg.senderId === currentUserId}
        />
      ))}
    </div>
  ))}
</div>
```

### SendMsgForm (`/src/views/apps/chat/SendMsgForm.tsx`)

**Type:** Client Component

**Props:**
```typescript
interface SendMsgFormProps {
  activeUser: ContactType | null
  onSendMessage: (message: string) => void
  disabled?: boolean
}
```

**Features:**
- Text input for message
- Send button
- Emoji support (optional)
- File attachment support (optional)
- Auto-focus handling
- Enter-to-send functionality

**Render:**
```
<form onSubmit={handleSend}>
  <TextField
    placeholder="Type a message..."
    value={messageText}
    onChange={(e) => setMessageText(e.target.value)}
    multiline
    maxRows={4}
  />
  <IconButton 
    type="submit" 
    disabled={!messageText.trim() || disabled}
  >
    <SendIcon />
  </IconButton>
</form>
```

### UserProfileRight (`/src/views/apps/chat/UserProfileRight.tsx`)

**Type:** Client Component

**Purpose:** Side drawer showing full user profile

**Features:**
- User avatar and full name
- User status
- About/bio section
- Role and department
- Contact information
- Block/report options
- Close button

**Render Structure:**
```
<Drawer anchor="right" open={open} onClose={onClose}>
  <UserProfileHeader user={user} />
  <Divider />
  <UserAbout about={user.about} />
  <Divider />
  <UserRoleInfo role={user.role} />
  <Divider />
  <UserActions
    onBlock={() => { /* block user */ }}
    onReport={() => { /* report user */ }}
  />
</Drawer>
```

### AvatarWithBadge (`/src/views/apps/chat/AvatarWithBadge.tsx`)

**Type:** Client Component

**Purpose:** Avatar with status badge indicator

**Props:**
```typescript
interface AvatarWithBadgeProps {
  alt: string
  src: string
  color: ThemeColor
  badgeColor: ThemeColor  // Status color
}
```

**Badge Positioning:**
- Bottom-right corner
- Color-coded by status (green=online, gray=offline, etc.)
- Animated pulse effect on online status

---

## Responsive Behavior

### Breakpoint Strategy

**Desktop (lg and above):**
- All three panels visible: Sidebar | Chat | Profile
- No backdrop
- Sidebar always open

**Tablet (md - lg):**
- Two visible: Sidebar | Chat (or Chat | Profile)
- Backdrop for navigation
- Sidebar toggleable
- Profile opens as overlay

**Mobile (below md):**
- One panel at a time
- Full-screen navigation
- Backdrop overlay
- Smooth transitions between panels

### Responsive Hooks Used

```typescript
const isBelowLgScreen = useMediaQuery((theme: Theme) => 
  theme.breakpoints.down('lg')
)
const isBelowMdScreen = useMediaQuery((theme: Theme) => 
  theme.breakpoints.down('md')
)
const isBelowSmScreen = useMediaQuery((theme: Theme) => 
  theme.breakpoints.down('sm')
)
```

### Layout Effects

```typescript
// Close backdrop when layout changes
useEffect(() => {
  if (!isBelowMdScreen && backdropOpen && sidebarOpen) {
    setBackdropOpen(false)
  }
}, [isBelowMdScreen])

// Open backdrop on small screens
useEffect(() => {
  if (!isBelowSmScreen && sidebarOpen) {
    setBackdropOpen(true)
  }
}, [isBelowSmScreen])

// Focus message input when user changes
useEffect(() => {
  if (chatStore.activeUser?.id !== null && messageInputRef.current) {
    messageInputRef.current.focus()
  }
}, [chatStore.activeUser])
```

---

## Utility Functions

### Date Formatting (`utils.ts`)

#### `isToday(date)`
Checks if a date is today

```typescript
const isToday = (date: Date | string) => {
  const today = new Date()
  return (
    new Date(date).getDate() === today.getDate() &&
    new Date(date).getMonth() === today.getMonth() &&
    new Date(date).getFullYear() === today.getFullYear()
  )
}
```

#### `formatDateToMonthShort(value, toTimeForCurrentDay)`
Format date for message timestamps

```typescript
export const formatDateToMonthShort = (
  value: Date | string,
  toTimeForCurrentDay = true
) => {
  const date = new Date(value)
  let formatting: Intl.DateTimeFormatOptions = {
    month: 'short',
    day: 'numeric'
  }

  // Show time for today's messages
  if (toTimeForCurrentDay && isToday(date)) {
    formatting = { hour: 'numeric', minute: 'numeric' }
  }

  return new Intl.DateTimeFormat('en-US', formatting).format(
    new Date(value)
  )
}
```

**Usage:**
```typescript
// Old messages: "Jan 15"
// Today's messages: "2:30 PM"
const timeDisplay = formatDateToMonthShort(message.timestamp)
```

---

## Type Definitions

### Chat Types

```typescript
// Chat data type
interface ChatDataType {
  activeUser: ContactType | null
  contacts: ContactType[]
  messages: ChatMessage[]
  userProfile: UserProfile | null
  loading: boolean
  error: string | null
  searchQuery: string
  selectedConversation: string | null
}

// Contact/user type
interface ContactType {
  id: number
  fullName: string
  avatar: string
  avatarColor: ThemeColor
  role: string
  status: 'online' | 'offline' | 'away' | 'busy'
  about?: string
  joinedDate?: Date
  lastSeen?: Date
}

// Message type
interface ChatMessage {
  id: string
  conversationId: string
  senderId: number
  receiverId: number
  message: string
  timestamp: Date
  isOwn: boolean
  attachments?: string[]
}

// User profile extended
interface UserProfile extends ContactType {
  email?: string
  phone?: string
  department?: string
  location?: string
  timezone?: string
}
```

---

## Data Flow

### Message Sending Flow

```
User types message
        ↓
onSend triggered in SendMsgForm
        ↓
dispatch(sendMessage(text))
        ↓
Redux Thunk makes API call (or stores locally)
        ↓
Reducer updates chatStore.messages
        ↓
ChatLog re-renders with new message
        ↓
Auto-scroll to bottom
        ↓
messageInputRef is refocused
```

### User Selection Flow

```
User clicks contact in SidebarLeft
        ↓
onSelectUser(contactId)
        ↓
dispatch(getActiveUserData(contactId))
        ↓
Redux Thunk fetches user + conversation
        ↓
Reducer sets activeUser, loads messages
        ↓
ChatContent updates with new conversation
        ↓
ChatLog renders new messages
        ↓
Focus message input ref
```

---

## Dependencies

### External Dependencies
- **React 18+** - Hooks (useRef, useEffect, useState, useMemo, useReducer)
- **Redux & react-redux** - State management
- **@mui/material** - UI components (Drawer, TextField, Button, etc.)
- **@mui/material/useMediaQuery** - Responsive breakpoints
- **@mui/material/useScrollTrigger** - Scroll detection
- **classnames** - Conditional CSS classes

### Internal Dependencies
- `@/redux-store` - Redux store and slices
- `@/redux-store/slices/chat` - Chat reducer and actions
- `@core/hooks/useSettings` - Settings context
- `@layouts/utils/layoutClasses` - Layout class utilities
- `@core/components/option-menu` - Dropdown menu
- `@core/components/mui/Avatar` - Avatar component
- Type definitions: `@/types/apps/chatTypes`

---

## Code Examples

### Dispatching Redux Action

```typescript
import { useDispatch, useSelector } from 'react-redux'
import { getActiveUserData } from '@/redux-store/slices/chat'

const ChatComponent = () => {
  const dispatch = useDispatch()
  const chatStore = useSelector(state => state.chatReducer)

  const handleSelectUser = (userId: number) => {
    dispatch(getActiveUserData(userId))
  }

  return (
    <div>
      {/* Component code */}
    </div>
  )
}
```

### Using formatDateToMonthShort

```typescript
import { formatDateToMonthShort } from './utils'

const MessageTimestamp = ({ timestamp }) => {
  return (
    <Typography variant="caption" color="textSecondary">
      {formatDateToMonthShort(timestamp)}
    </Typography>
  )
}
```

### Responsive Sidebar with useMediaQuery

```typescript
import useMediaQuery from '@mui/material/useMediaQuery'
import type { Theme } from '@mui/material/styles'

const ChatSidebar = () => {
  const isBelowLgScreen = useMediaQuery((theme: Theme) =>
    theme.breakpoints.down('lg')
  )

  return (
    <Drawer
      variant={isBelowLgScreen ? 'temporary' : 'permanent'}
      open={isBelowLgScreen ? sidebarOpen : true}
    >
      {/* Sidebar content */}
    </Drawer>
  )
}
```

### Message Grouping with useMemo

```typescript
const groupedMessages = useMemo(() => {
  return messages.reduce((acc, msg) => {
    const dateKey = formatDateToMonthShort(msg.timestamp)
    if (!acc[dateKey]) {
      acc[dateKey] = []
    }
    acc[dateKey].push(msg)
    return acc
  }, {} as Record<string, ChatMessage[]>)
}, [messages])

return (
  <>
    {Object.entries(groupedMessages).map(([date, msgs]) => (
      <div key={date}>
        <Typography align="center" variant="caption">
          {date}
        </Typography>
        {msgs.map(msg => (
          <MessageBubble key={msg.id} message={msg} />
        ))}
      </div>
    ))}
  </>
)
```

---

## Summary

The Chat App domain provides:

- **Real-time messaging** - Send/receive messages between users
- **User presence** - Online/offline/away/busy status tracking
- **Contact management** - Contact list with search
- **Message history** - Persistent message storage with grouping
- **User profiles** - Detailed user information views
- **Responsive layout** - Multi-panel adaptive design
- **Redux state** - Centralized state management
- **Ref management** - Focus control on message input

**Key Statistics:**
- 9 components
- 2+ Redux slices
- 4+ responsive breakpoints
- 10+ state management actions
- ~2,000 LOC
- 6+ custom hooks usage patterns
- 5+ utility functions

**Benefits:**
- **Scalable state** - Redux for complex multi-panel state
- **Responsive** - Smooth transitions across breakpoints
- **Type-safe** - TypeScript interfaces for all data
- **Performance** - useMemo for expensive operations
- **User experience** - Auto-focus, smooth animations
- **Extensible** - Easy to add features (typing indicators, reactions, etc.)
