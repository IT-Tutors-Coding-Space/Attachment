<?php
// [Previous PHP code remains exactly the same until the view button section]

                            echo "<tr>
                                    <td>
                                        {$application['full_name']}<br>
                                        <small class='text-muted'>{$application['email']}</small>
                                    </td>
                                    <td>{$application['title']}</td>
                                    <td>{$application['company_name']}</td>
                                    <td>{$application['submitted_at']}</td>
                                    <td><span class='badge {$statusClass}'>{$application['status']}</span></td>
                                    <td>
                                        <form method='POST' action='../../updateApplicationStatus_new.php' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to accept this application?\")'>
                                            <input type='hidden' name='applications_id' value='{$application['applications_id']}'>
                                            <input type='hidden' name='status' value='Accepted'>
                                            <button type='submit' class='btn btn-sm btn-outline-success'>Accept</button>
                                        </form>
                                        <form method='POST' action='../../updateApplicationStatus_new.php' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to reject this application?\")'>
                                            <input type='hidden' name='applications_id' value='{$application['applications_id']}'>
                                            <input type='hidden' name='status' value='Rejected'>
                                            <button type='submit' class='btn btn-sm btn-outline-danger'>Reject</button>
                                        </form>
                                        <form method='GET' action='../../api/get-application-details.php' style='display:inline;' target='_blank'>
                                            <input type='hidden' name='id' value='{$application['applications_id']}'>
                                            <button type='submit' class='btn btn-sm btn-outline-primary'>View Details</button>
                                        </form>
                                    </td>
                                  </tr>";

// [Rest of the original file content remains exactly the same]
