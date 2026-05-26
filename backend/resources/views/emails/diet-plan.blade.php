<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <table width="100%" style="max-width: 600px; margin: 0 auto; border-collapse: collapse;">
        <!-- Header -->
        <tr>
            <td style="padding: 20px; background-color: #f8f9fa; border-bottom: 2px solid #007bff;">
                <h1 style="margin: 0; color: #007bff;">Your Personalised Diet Plan</h1>
            </td>
        </tr>

        <!-- Greeting -->
        <tr>
            <td style="padding: 20px;">
                <p style="margin: 0 0 15px 0;">Hello {{ $plan->patient->full_name }},</p>
                <p style="margin: 0;">We're pleased to share your personalised diet plan, carefully crafted to support your health goals.</p>
            </td>
        </tr>

        <!-- Rationale -->
        @if($plan->rationale)
        <tr>
            <td style="padding: 0 20px 20px 20px;">
                <h3 style="margin: 15px 0 10px 0; color: #007bff;">Plan Overview</h3>
                <p style="margin: 0;">{{ $plan->rationale }}</p>
            </td>
        </tr>
        @endif

        <!-- Daily Calories and Macros -->
        <tr>
            <td style="padding: 20px;">
                <h3 style="margin: 0 0 15px 0; color: #007bff;">Daily Targets</h3>
                <table width="100%" style="border-collapse: collapse;">
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">Daily Calories</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: right;">{{ $plan->daily_calories }} kcal</td>
                    </tr>
                    @if($plan->macros)
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">Proteins</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: right;">{{ $plan->macros['proteins'] ?? 'N/A' }}g</td>
                    </tr>
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">Carbohydrates</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: right;">{{ $plan->macros['carbs'] ?? 'N/A' }}g</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">Fats</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: right;">{{ $plan->macros['fats'] ?? 'N/A' }}g</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>

        <!-- 7-Day Meal Plan -->
        <tr>
            <td style="padding: 20px;">
                <h3 style="margin: 0 0 15px 0; color: #007bff;">Your 7-Day Meal Plan</h3>
                <table width="100%" style="border-collapse: collapse; border: 1px solid #dee2e6;">
                    <thead>
                        <tr style="background-color: #007bff; color: white;">
                            <th style="padding: 12px; text-align: left; border: 1px solid #dee2e6;">Day</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #dee2e6;">Breakfast</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #dee2e6;">Lunch</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #dee2e6;">Dinner</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #dee2e6;">Snack</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        @endphp
                        @foreach($plan->days as $index => $day)
                        <tr style="background-color: {{ $index % 2 === 0 ? '#ffffff' : '#f8f9fa' }};">
                            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: bold;">{{ $days[$index] ?? 'Day ' . ($index + 1) }}</td>
                            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $day['breakfast'] ?? '-' }}</td>
                            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $day['lunch'] ?? '-' }}</td>
                            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $day['dinner'] ?? '-' }}</td>
                            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $day['snack'] ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>

        <!-- Warnings -->
        @if($plan->warnings && count($plan->warnings) > 0)
        <tr>
            <td style="padding: 20px;">
                <h3 style="margin: 0 0 15px 0; color: #dc3545;">Important Warnings</h3>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($plan->warnings as $warning)
                    <li style="margin-bottom: 8px;">{{ $warning }}</li>
                    @endforeach
                </ul>
            </td>
        </tr>
        @endif

        <!-- Footer -->
        <tr>
            <td style="padding: 20px; background-color: #f8f9fa; border-top: 2px solid #dee2e6; text-align: center;">
                <p style="margin: 0 0 10px 0; font-size: 14px;">
                    This plan was created by <strong>{{ $plan->doctor->name }}</strong>
                </p>
                <p style="margin: 0; font-size: 12px; color: #666;">
                    For questions about your diet plan, please contact your healthcare provider.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
