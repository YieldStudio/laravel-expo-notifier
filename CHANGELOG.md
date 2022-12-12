# Changelog

All notable changes to `laravel-expo-notifier` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## 0.0.3 - 2022-12-12

### What's Changed

- Add subtitle to ExpoMessage
- Add mutableContent to ExpoMessage
- Fix ExpoMessage serialization
- Channel no longer send notifications if `to` is empty
- Fix notify method of service to serialize messages as Expo message object

**Full Changelog**: https://github.com/YieldStudio/laravel-expo-notifier/compare/0.0.2...0.0.3

## 0.0.2 - 2022-11-23

### What's Changed

- Add possibility to delete one value by @dtangdev in https://github.com/YieldStudio/laravel-expo-notifier/pull/5

**Full Changelog**: https://github.com/YieldStudio/laravel-expo-notifier/compare/0.0.1...0.0.2

## 0.0.1 - 2022-11-18

### What's Changed

- First release 🎉
- Automatically batch non-urgent notifications
- Check push receipts to clear bad tokens

### New Contributors

- @dtangdev made their first contribution in https://github.com/YieldStudio/laravel-expo-notifier/pull/1

**Full Changelog**: https://github.com/YieldStudio/laravel-expo-notifier/commits/0.0.1
